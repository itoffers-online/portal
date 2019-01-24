<?php

namespace HireInSocial;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

function dbal(Config $config) : Connection
{
    return DriverManager::getConnection(
        [
            'dbname' => $config->getString(Config::DB_NAME),
            'user' => $config->getString(Config::DB_USER),
            'password' => $config->getString(Config::DB_USER_PASS),
            'host' => $config->getString(Config::DB_HOST),
            'port' => $config->getInt(Config::DB_PORT),
            'driver' => 'pdo_pgsql',
        ],
        new Configuration()
    );
}
