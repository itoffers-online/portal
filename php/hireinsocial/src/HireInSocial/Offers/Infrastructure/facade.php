<?php

declare(strict_types=1);

/*
 * This file is part of the Hire in Social project.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HireInSocial\Offers\Infrastructure;

use Facebook\Facebook;
use HireInSocial\Offers\Application\Command\Offer\ApplyThroughEmailHandler;
use HireInSocial\Offers\Application\Command\Offer\PostOfferHandler;
use HireInSocial\Offers\Application\Command\Offer\RemoveOfferHandler;
use HireInSocial\Offers\Application\Command\Specialization\CreateSpecializationHandler;
use HireInSocial\Offers\Application\Command\Specialization\RemoveFacebookChannelHandler;
use HireInSocial\Offers\Application\Command\Specialization\RemoveTwitterChannelHandler;
use HireInSocial\Offers\Application\Command\Specialization\SetFacebookChannelHandler;
use HireInSocial\Offers\Application\Command\Specialization\SetTwitterChannelHandler;
use HireInSocial\Offers\Application\Command\User\AddExtraOffersHandler;
use HireInSocial\Offers\Application\Command\User\BlockUserHandler;
use HireInSocial\Offers\Application\Command\User\FacebookConnectHandler;
use HireInSocial\Offers\Application\Config;
use HireInSocial\Offers\Application\Facebook\FacebookFormatter;
use HireInSocial\Offers\Application\Facebook\FacebookGroupService;
use HireInSocial\Offers\Application\Offer\EmailFormatter;
use HireInSocial\Offers\Application\Offer\Throttling;
use HireInSocial\Offers\Application\System;
use HireInSocial\Offers\Application\System\CommandBus;
use HireInSocial\Offers\Application\System\Queries;
use HireInSocial\Offers\Infrastructure\Doctrine\DBAL\Application\Offer\DbalApplicationQuery;
use HireInSocial\Offers\Infrastructure\Doctrine\DBAL\Application\Offer\DbalOfferQuery;
use HireInSocial\Offers\Infrastructure\Doctrine\DBAL\Application\Offer\DbalOfferThrottleQuery;
use HireInSocial\Offers\Infrastructure\Doctrine\DBAL\Application\SocialChannel\Facebook\DbalFacebookFacebookQuery;
use HireInSocial\Offers\Infrastructure\Doctrine\DBAL\Application\Specialization\DbalSpecializationQuery;
use HireInSocial\Offers\Infrastructure\Doctrine\DBAL\Application\User\DbalExtraOffersQuery;
use HireInSocial\Offers\Infrastructure\Doctrine\DBAL\Application\User\DbalUserQuery;
use HireInSocial\Offers\Infrastructure\Doctrine\ORM\Application\Facebook\ORMPosts;
use HireInSocial\Offers\Infrastructure\Doctrine\ORM\Application\Offer\ORMApplications;
use HireInSocial\Offers\Infrastructure\Doctrine\ORM\Application\Offer\ORMOfferPDFs;
use HireInSocial\Offers\Infrastructure\Doctrine\ORM\Application\Offer\ORMOffers;
use HireInSocial\Offers\Infrastructure\Doctrine\ORM\Application\Offer\ORMSlugs;
use HireInSocial\Offers\Infrastructure\Doctrine\ORM\Application\Specialization\ORMSpecializations;
use HireInSocial\Offers\Infrastructure\Doctrine\ORM\Application\System\ORMTransactionManager;
use HireInSocial\Offers\Infrastructure\Doctrine\ORM\Application\User\ORMExtraOffers;
use HireInSocial\Offers\Infrastructure\Doctrine\ORM\Application\User\ORMUsers;
use HireInSocial\Offers\Infrastructure\Facebook\FacebookGraphSDK;
use HireInSocial\Offers\Infrastructure\Flysystem\Application\System\FlysystemStorage;
use HireInSocial\Offers\Infrastructure\PHP\Hash\SHA256Encoder;
use HireInSocial\Offers\Infrastructure\PHP\SystemCalendar\SystemCalendar;
use HireInSocial\Offers\Infrastructure\SwiftMailer\System\SwiftMailer;
use HireInSocial\Offers\Offers;
use HireInSocial\Tests\Offers\Application\Double\Dummy\DummyFacebook;
use HireInSocial\Tests\Offers\Application\Double\Stub\CalendarStub;
use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

function offersFacade(Config $config) : Offers
{
    $logDir = $config->getString(Config::ROOT_PATH) . '/var/logs';

    $systemLogger = new Logger('system');
    $systemLogger->pushHandler(new StreamHandler($logDir . sprintf('/%s_system.log', $config->getString(Config::ENV)), Logger::DEBUG));

    $loader = new FilesystemLoader($config->getString(Config::ROOT_PATH) . '/resources/templates/' . $config->getString(Config::LOCALE));
    $twig = new Environment($loader, [
        'cache' => $config->getString(Config::ROOT_PATH) . '/var/cache/' . $config->getString(Config::ENV) . '/twig',
        'debug' => $config->getString(Config::ENV) !== 'prod',
        'auto_reload' => $config->getString(Config::ENV) !== 'prod',
    ]);
    $twig->addGlobal('apply_email_template', $config->getString(Config::APPLY_EMAIL_TEMPLATE));
    $twig->addGlobal('domain', $config->getString(Config::DOMAIN));

    switch ($config->getString(Config::ENV)) {
        case 'prod':
            $calendar = new SystemCalendar(new \DateTimeZone('UTC'));
            $facebook = new FacebookGraphSDK(
                new Facebook([
                    'app_id' => $config->getString(Config::FB_APP_ID),
                    'app_secret' => $config->getString(Config::FB_APP_SECRET),
                ]),
                $systemLogger
            );
            $transport = (new \Swift_SmtpTransport(
                $config->getJson(Config::MAILER_CONFIG)['host'],
                $config->getJson(Config::MAILER_CONFIG)['port']
            ))
                ->setUsername($config->getJson(Config::MAILER_CONFIG)['username'])
                ->setPassword($config->getJson(Config::MAILER_CONFIG)['password'])
                ->setTimeout(10)
            ;
            $mailer = new SwiftMailer($config->getJson(Config::MAILER_CONFIG)['domain'], new \Swift_Mailer($transport));
            ErrorHandler::register($systemLogger);

            break;
        case 'dev':
            $calendar = new CalendarStub(new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
            $facebook = new FacebookGraphSDK(
                new Facebook([
                    'app_id' => $config->getString(Config::FB_APP_ID),
                    'app_secret' => $config->getString(Config::FB_APP_SECRET),
                ]),
                $systemLogger
            );

            $transport = (new \Swift_SmtpTransport(
                $config->getJson(Config::MAILER_CONFIG)['host'],
                $config->getJson(Config::MAILER_CONFIG)['port']
            ))
                ->setUsername($config->getJson(Config::MAILER_CONFIG)['username'])
                ->setPassword($config->getJson(Config::MAILER_CONFIG)['password'])
                ->setTimeout(10)
            ;
            $mailer = new SwiftMailer($config->getJson(Config::MAILER_CONFIG)['domain'], new \Swift_Mailer($transport));

            break;
        case 'test':
            $calendar = new CalendarStub(new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
            $facebook = new DummyFacebook();

            $transport = new \Swift_Transport_NullTransport(new \Swift_Events_SimpleEventDispatcher());

            $mailer = new SwiftMailer(
                $config->getJson(Config::MAILER_CONFIG)['domain'],
                new \Swift_Mailer($transport)
            );

            break;
        default:
            throw new \RuntimeException(sprintf('Unknown environment %s', $config->getString(Config::ENV)));
    }

    $dbalConnection = dbal($config);
    $entityManager = orm($config, $dbalConnection);
    $specializations = new ORMSpecializations($entityManager);

    $throttling = Throttling::createDefault($calendar);
    $ormOffers = new ORMOffers($entityManager);
    $ormUsers = new ORMUsers($entityManager);
    $ormExtraOffers = new ORMExtraOffers($entityManager);
    $ormApplications = new ORMApplications($entityManager);

    $encoder = new SHA256Encoder();
    $emailFormatter = new EmailFormatter($twig);

    return new Offers(
        new System(
            new CommandBus(
                new ORMTransactionManager($entityManager),
                new CreateSpecializationHandler(
                    $specializations
                ),
                new SetFacebookChannelHandler(
                    $specializations
                ),
                new SetTwitterChannelHandler(
                    $specializations
                ),
                new RemoveFacebookChannelHandler(
                    $specializations
                ),
                new RemoveTwitterChannelHandler(
                    $specializations
                ),
                new PostOfferHandler(
                    $calendar,
                    $ormOffers,
                    $ormExtraOffers,
                    $ormUsers,
                    new ORMPosts($entityManager),
                    $throttling,
                    new FacebookGroupService($facebook),
                    new FacebookFormatter($twig),
                    $specializations,
                    new ORMSlugs($entityManager),
                    new ORMOfferPDFs($entityManager),
                    FlysystemStorage::create($config->getJson(Config::FILESYSTEM_CONFIG))
                ),
                new RemoveOfferHandler(
                    $ormUsers,
                    $ormOffers,
                    $calendar
                ),
                new FacebookConnectHandler(
                    $ormUsers,
                    $calendar
                ),
                new BlockUserHandler(
                    $ormUsers,
                    $calendar
                ),
                new AddExtraOffersHandler(
                    $ormUsers,
                    $ormExtraOffers,
                    $calendar
                ),
                new ApplyThroughEmailHandler(
                    $mailer,
                    $ormOffers,
                    $ormApplications,
                    $encoder,
                    $emailFormatter,
                    $calendar
                )
            ),
            new Queries(
                new DbalOfferThrottleQuery($throttling->limit(), $throttling->since(), $dbalConnection, $calendar),
                new DbalOfferQuery($dbalConnection),
                new DbalSpecializationQuery($dbalConnection),
                new DbalUserQuery($dbalConnection),
                new DbalExtraOffersQuery($dbalConnection),
                new DbalApplicationQuery($dbalConnection, $encoder),
                new DbalFacebookFacebookQuery($dbalConnection),
            ),
            $systemLogger,
            $calendar
        ),
        $calendar
    );
}
