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

namespace HireInSocial\Infrastructure;

use Facebook\Facebook;

use HireInSocial\Application\Command\Offer;
use HireInSocial\Application\Command\Specialization;
use HireInSocial\Application\Command\User;
use HireInSocial\Application\Config;
use HireInSocial\Application\Facebook\FacebookFormatter;
use HireInSocial\Application\Facebook\FacebookGroupService;
use HireInSocial\Application\Offer\EmailFormatter;
use HireInSocial\Application\Offer\Throttling;
use HireInSocial\Application\System;
use HireInSocial\Application\System\CommandBus;
use HireInSocial\Application\System\Queries;
use HireInSocial\Infrastructure\Doctrine\DBAL\Application\Offer\DbalApplicationQuery;
use HireInSocial\Infrastructure\Doctrine\DBAL\Application\Offer\DbalOfferQuery;
use HireInSocial\Infrastructure\Doctrine\DBAL\Application\Offer\DbalOfferThrottleQuery;
use HireInSocial\Infrastructure\Doctrine\DBAL\Application\Specialization\DbalSpecializationQuery;
use HireInSocial\Infrastructure\Doctrine\DBAL\Application\User\DbalUserQuery;
use HireInSocial\Infrastructure\Doctrine\ORM\Application\Facebook\ORMPosts;
use HireInSocial\Infrastructure\Doctrine\ORM\Application\Offer\ORMApplications;
use HireInSocial\Infrastructure\Doctrine\ORM\Application\Offer\ORMOfferPDFs;
use HireInSocial\Infrastructure\Doctrine\ORM\Application\Offer\ORMOffers;
use HireInSocial\Infrastructure\Doctrine\ORM\Application\Offer\ORMSlugs;
use HireInSocial\Infrastructure\Doctrine\ORM\Application\Specialization\ORMSpecializations;
use HireInSocial\Infrastructure\Doctrine\ORM\Application\System\ORMTransactionManager;
use HireInSocial\Infrastructure\Doctrine\ORM\Application\User\ORMUsers;
use HireInSocial\Infrastructure\Facebook\FacebookGraphSDK;
use HireInSocial\Infrastructure\Flysystem\Application\System\FlysystemStorage;
use HireInSocial\Infrastructure\PHP\Hash\SHA256Encoder;
use HireInSocial\Infrastructure\PHP\SystemCalendar\SystemCalendar;
use HireInSocial\Infrastructure\SwiftMailer\System\SwiftMailer;
use HireInSocial\Offers;
use HireInSocial\Tests\Application\Double\Dummy\DummyFacebook;
use HireInSocial\Tests\Application\Double\Stub\CalendarStub;
use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

function offersFacade(Config $config) : Offers
{
    $logDir = $config->getString(Config::ROOT_PATH) . '/var/logs';

    $phpLogger = new Logger('php');
    $systemLogger = new Logger('system');
    $facebookLogger = new Logger('facebook');
    $systemLogger->pushHandler(new StreamHandler($logDir . sprintf('/%s_system.log', $config->getString(Config::ENV)), Logger::DEBUG));
    $facebookLogger->pushHandler(new StreamHandler($logDir . sprintf('/%s_facebook.log', $config->getString(Config::ENV)), Logger::DEBUG));
    $phpLogger->pushHandler(new StreamHandler($logDir . sprintf('/%s_php.log', $config->getString(Config::ENV)), Logger::ERROR));

    $loader = new FilesystemLoader($config->getString(Config::ROOT_PATH) . '/resources/templates/' . $config->getString(Config::LOCALE));
    $twig = new Environment($loader, [
        'cache' => $config->getString(Config::ROOT_PATH) . '/var/cache/' . $config->getString(Config::ENV) . '/twig',
        'debug' => $config->getString(Config::ENV) !== 'prod',
    ]);

    switch ($config->getString(Config::ENV)) {
        case 'prod':
            $calendar = new SystemCalendar(new \DateTimeZone('UTC'));
            $facebook = new FacebookGraphSDK(
                new Facebook([
                    'app_id' => $config->getString(Config::FB_APP_ID),
                    'app_secret' => $config->getString(Config::FB_APP_SECRET),
                ]),
                $facebookLogger
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
            ErrorHandler::register($phpLogger);

            break;
        case 'dev':
            $calendar = new CalendarStub(new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
            $facebook = new FacebookGraphSDK(
                new Facebook([
                    'app_id' => $config->getString(Config::FB_APP_ID),
                    'app_secret' => $config->getString(Config::FB_APP_SECRET),
                ]),
                $facebookLogger
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
    $ormApplications = new ORMApplications($entityManager);

    $encoder = new SHA256Encoder();
    $emailFormatter = new EmailFormatter($twig);

    return new Offers(
        new System(
            new CommandBus(
                new ORMTransactionManager($entityManager),
                new Specialization\CreateSpecializationHandler(
                    $specializations
                ),
                new Specialization\SetFacebookChannelHandler(
                    $specializations
                ),
                new Specialization\RemoveFacebookChannelHandler(
                    $specializations
                ),
                new Offer\PostOfferHandler(
                    $calendar,
                    $ormOffers,
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
                new Offer\RemoveOfferHandler(
                    $ormUsers,
                    $ormOffers,
                    $calendar
                ),
                new User\FacebookConnectHandler(
                    $ormUsers,
                    $calendar
                ),
                new Offer\ApplyThroughEmailHandler(
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
                new DbalApplicationQuery($dbalConnection, $encoder)
            ),
            $systemLogger,
            $calendar
        ),
        $calendar
    );
}
