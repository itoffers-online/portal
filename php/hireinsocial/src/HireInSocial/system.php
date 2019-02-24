<?php

namespace HireInSocial;

use Facebook\Facebook;

use HireInSocial\Application\Command\Offer;
use HireInSocial\Application\Command\Specialization;
use HireInSocial\Application\Command\Throttle;
use HireInSocial\Application\Command\User;
use HireInSocial\Application\Config;
use HireInSocial\Application\Facebook\FacebookFormatter;
use HireInSocial\Application\Facebook\FacebookGroupService;
use HireInSocial\Infrastructure\Doctrine\DBAL\Application\Offer\DbalOfferQuery;
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
use HireInSocial\Infrastructure\Predis\PredisThrottle;
use HireInSocial\Application\Query\Offer\OfferThrottleQuery;
use HireInSocial\Application\System;
use HireInSocial\Application\System\CommandBus;
use HireInSocial\Application\System\Queries;
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

    $throttleDuration = new \DateInterval($config->getString(Config::THROTTLE_DURATION));
    $predis = new \Predis\Client($config->getString(Config::REDIS_DSN) . '/' . Config::REDIS_DB_SYSTEM);

    switch ($config->getString(Config::ENV)) {
        case 'prod':
            $calendar = new SystemCalendar(new \DateTimeZone('UTC'));
            $offerThrottle = new PredisThrottle($predis, $calendar, $throttleDuration, 'job-offer-user-prod-');
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
            $offerThrottle = new PredisThrottle($predis, $calendar, $throttleDuration, 'job-offer-user-dev-');
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
            $offerThrottle = new PredisThrottle($predis, $calendar, $throttleDuration, 'job-offer-user-test-');
            $facebook = new DummyFacebook();

            break;
        default:
            throw new \RuntimeException(sprintf('Unknown environment %s', $config->getString(Config::ENV)));
    }

    $dbalConnection = dbal($config);
    $entityManager = orm($config, $dbalConnection);

    return new System(
        new CommandBus(
            new ORMTransactionManager($entityManager),
            new Specialization\CreateSpecializationHandler(
                new ORMSpecializations($entityManager)
            ),
            new Offer\PostOfferHandler(
                $calendar,
                new ORMOffers($entityManager),
                new ORMUsers($entityManager),
                new ORMPosts($entityManager),
                new FacebookGroupService($facebook),
                new FacebookFormatter($twig),
                new ORMSpecializations($entityManager),
                $offerThrottle,
                new ORMSlugs($entityManager)
            ),
            new Throttle\RemoveThrottleHandler(
                $offerThrottle
            ),
            new User\FacebookConnectHandler(
                new ORMUsers($entityManager),
                $calendar
            )
        ),
        new Queries(
            new OfferThrottleQuery($offerThrottle),
            new DbalOfferQuery($dbalConnection),
            new DBALSpecializationQuery($dbalConnection),
            new DBALUserQuery($dbalConnection)
        ),
        $systemLogger,
        $calendar
    );
}
