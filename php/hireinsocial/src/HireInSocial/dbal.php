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

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use HireInSocial\Application\Config;
use HireInSocial\Infrastructure\Doctrine\DBAL\Types\Offer\SalaryType;

function dbal(Config $config) : Connection
{
    if (!Type::hasType(SalaryType::NAME)) {
        Type::addType(SalaryType::NAME, SalaryType::class);
    }

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
