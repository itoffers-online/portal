<?php

namespace HireInSocial;

use Facebook\Facebook;


use HireInSocial\Application\Command\Facebook\Page\PostToGroupHandler;
use HireInSocial\Application\Facebook\FacebookFormatter;
use HireInSocial\Application\Facebook\FacebookGroupService;
use HireInSocial\Application\Facebook\Group;
use HireInSocial\Application\Facebook\Page;
use HireInSocial\Infrastructure\Doctrine\DBAL\Application\Facebook\DBALPosts;
use HireInSocial\Infrastructure\Doctrine\DBAL\Application\Offer\DBALOffers;
use HireInSocial\Infrastructure\Facbook\FacebookGraphSDK;
use HireInSocial\Infrastructure\InMemory\InMemoryThrottle;
use HireInSocial\Infrastructure\PHP\SystemCalendar\SystemCalendar;
use HireInSocial\Infrastructure\Predis\PredisThrottle;
use HireInSocial\Application\Query\Offer\OfferThrottleQuery;
use HireInSocial\Application\System;
use HireInSocial\Application\System\CommandBus;
use HireInSocial\Application\System\Queries;
use HireInSocial\Tests\Application\Double\Dummy\DummyFacebook;
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

    $calendar = new SystemCalendar(new \DateTimeZone('UTC'));

    $throttleDuration = new \DateInterval($config->getString(Config::THROTTLE_DURATION));
    $predis = new \Predis\Client($config->getString(Config::REDIS_DSN));

    switch ($config->getString(Config::ENV)) {
        case 'prod':
        case 'dev':
            $offerThrottle = new PredisThrottle($predis, $calendar, $throttleDuration, 'job-offer-user-');
            $facebookGraphSDK = new FacebookGraphSDK(
                new Facebook([
                    'app_id' => $config->getString(Config::FB_APP_ID),
                    'app_secret' => $config->getString(Config::FB_APP_SECRET),
                ]),
                $facebookLogger
            );

            break;
        case 'test':
            $offerThrottle = new InMemoryThrottle();
            $facebookGraphSDK = new DummyFacebook();

            break;
        default:
            throw new \RuntimeException(sprintf('Unknown environment %s', $config->getString(Config::ENV)));
    }

    $dbalConnection = dbal($config);

    return new System(
        new CommandBus(
            new PostToGroupHandler(
                $calendar,
                new DBALOffers($dbalConnection),
                new DBALPosts($dbalConnection),
                new FacebookGroupService(
                    $facebookGraphSDK,
                    $offerThrottle
                ),
                new FacebookFormatter($twig),
                new Group($config->getString(Config::FB_GROUP_ID)),
                new Page($config->getString(Config::FB_PAGE_ID), $config->getString(Config::FB_PAGE_TOKEN))
            )
        ),
        new Queries(
            new OfferThrottleQuery($offerThrottle)
        ),
        $systemLogger
    );
}
