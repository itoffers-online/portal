<?php

namespace HireInSocial;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;
use Doctrine\ORM\Proxy\ProxyFactory;
use HireInSocial\Application\Config;

function orm(Config $config, Connection $connection) : EntityManager
{
    $configuration = new Configuration();
    $isDevMode = $config->getString(Config::ENV) !== 'prod';

    $configuration->setMetadataDriverImpl(new SimplifiedXmlDriver(
        [
             $config->getString(Config::ROOT_PATH) . '/db/orm/mapping/xml' => 'HireInSocial\Application',
        ]
    ));

    $configuration->setNamingStrategy(new \Doctrine\ORM\Mapping\UnderscoreNamingStrategy(CASE_LOWER));

    if ($isDevMode) {
        $cache = new \Doctrine\Common\Cache\ArrayCache;
    } else {
        $cache = new \Doctrine\Common\Cache\PredisCache(new \Predis\Client($config->getString(Config::REDIS_DSN) . '/' . Config::REDIS_DB_DOCTRINE_CACHE));
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
