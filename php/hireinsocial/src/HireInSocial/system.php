<?php

/*
 * This file is part of the Hire in Social project.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HireInSocial;

use Facebook\Facebook;

use HireInSocial\Application\Command\Offer;
use HireInSocial\Application\Command\Specialization;
use HireInSocial\Application\Command\User;
use HireInSocial\Application\Config;
use HireInSocial\Application\Facebook\FacebookFormatter;
use HireInSocial\Application\Facebook\FacebookGroupService;
use HireInSocial\Application\Offer\Throttling;
use HireInSocial\Application\System;
use HireInSocial\Application\System\CommandBus;
use HireInSocial\Application\System\Queries;
use HireInSocial\Infrastructure\Doctrine\DBAL\Application\Offer\DbalOfferQuery;
use HireInSocial\Infrastructure\Doctrine\DBAL\Application\Offer\DbalOfferThrottleQuery;
use HireInSocial\Infrastructure\Doctrine\DBAL\Application\Specialization\DBALSpecializationQuery;
use HireInSocial\Infrastructure\Doctrine\DBAL\Application\User\DBALUserQuery;
use HireInSocial\Infrastructure\Doctrine\ORM\Application\Facebook\ORMPosts;
use HireInSocial\Infrastructure\Doctrine\ORM\Application\Offer\ORMOffers;
use HireInSocial\Infrastructure\Doctrine\ORM\Application\Offer\ORMSlugs;
use HireInSocial\Infrastructure\Doctrine\ORM\Application\Specialization\ORMSpecializations;
use HireInSocial\Infrastructure\Doctrine\ORM\Application\System\ORMTransactionManager;
use HireInSocial\Infrastructure\Doctrine\ORM\Application\User\ORMUsers;
use HireInSocial\Infrastructure\Facebook\FacebookGraphSDK;
use HireInSocial\Infrastructure\PHP\SystemCalendar\SystemCalendar;
use HireInSocial\Tests\Application\Double\Dummy\DummyFacebook;
use HireInSocial\Tests\Application\Double\Stub\CalendarStub;
use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

function system(Config $config) : System
{
    $logDir = $config->getString(Config::ROOT_PATH) . '/var/logs';

    $phpLogger = new Logger('php');
    $systemLogger = new Logger('system');
    $facebookLogger = new Logger('facebook');
    $systemLogger->pushHandler(new StreamHandler($logDir . sprintf('/%s_system.log', $config->getString(Config::ENV)), Logger::DEBUG));
    $facebookLogger->pushHandler(new StreamHandler($logDir . sprintf('/%s_facebook.log', $config->getString(Config::ENV)), Logger::DEBUG));
    $phpLogger->pushHandler(new StreamHandler($logDir . sprintf('/%s_php.log', $config->getString(Config::ENV)), Logger::ERROR));
    ErrorHandler::register($phpLogger);

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

            break;
        case 'test':
            $calendar = new CalendarStub(new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
            $facebook = new DummyFacebook();

            break;
        default:
            throw new \RuntimeException(sprintf('Unknown environment %s', $config->getString(Config::ENV)));
    }

    $dbalConnection = dbal($config);
    $entityManager = orm($config, $dbalConnection);
    $specializations = new ORMSpecializations($entityManager);

    $throttling = Throttling::createDefault($calendar);

    return new System(
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
                new ORMOffers($entityManager),
                new ORMUsers($entityManager),
                new ORMPosts($entityManager),
                $throttling,
                new FacebookGroupService($facebook),
                new FacebookFormatter($twig),
                $specializations,
                new ORMSlugs($entityManager)
            ),
            new User\FacebookConnectHandler(
                new ORMUsers($entityManager),
                $calendar
            )
        ),
        new Queries(
            new DbalOfferThrottleQuery($throttling->limit(), $throttling->since(), $dbalConnection, $calendar),
            new DbalOfferQuery($dbalConnection),
            new DBALSpecializationQuery($dbalConnection),
            new DBALUserQuery($dbalConnection)
        ),
        $systemLogger,
        $calendar
    );
}
