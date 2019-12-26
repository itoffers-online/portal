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

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\PredisCache;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\Proxy\ProxyFactory;
use HireInSocial\Application\Config;
use Predis\Client;

function orm(Config $config, Connection $connection) : EntityManager
{
    $configuration = new Configuration();
    $isDevMode = $config->getString(Config::ENV) !== 'prod';

    $configuration->setMetadataDriverImpl(new SimplifiedXmlDriver(
        [
             $config->getString(Config::ROOT_PATH) . '/db/orm/mapping/xml' => 'HireInSocial\Application',
        ]
    ));

    $configuration->setNamingStrategy(new UnderscoreNamingStrategy(CASE_LOWER));

    if ($isDevMode) {
        $cache = new ArrayCache;
    } else {
        $cache = new PredisCache(new Client($config->getString(Config::REDIS_DSN) . '/' . Config::REDIS_DB_DOCTRINE_CACHE));
    }

    $configuration->setMetadataCacheImpl($cache);
    $configuration->setQueryCacheImpl($cache);

    $configuration->setProxyDir($config->getString(Config::ROOT_PATH) . '/var/cache/orm');
    $configuration->setProxyNamespace('DoctrineProxy');
    $configuration->setAutoGenerateProxyClasses($isDevMode);

    if ($isDevMode) {
        $configuration->setAutoGenerateProxyClasses(ProxyFactory::AUTOGENERATE_EVAL);
    }

    return EntityManager::create($connection, $configuration);
}
