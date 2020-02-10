<?php

declare(strict_types=1);

/*
 * This file is part of the itoffers.online project.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ITOffers\Offers\Infrastructure;

use ITOffers\Config;
use Symfony\Component\Dotenv\Dotenv;

function bootstrap(string $projectRootPath) : Config
{
    if (!\file_exists($projectRootPath)) {
        die(sprintf('Invalid project root path: %s', $projectRootPath));
    }

    if (getenv('ITOF_ENV') === 'test') {
        $dotEnv = new Dotenv();
        $dotEnv->load($projectRootPath . '/.env.test');
    } else {
        if (\file_exists($projectRootPath . '/.env')) {
            $dotEnv = new Dotenv();
            $dotEnv->load($projectRootPath . '/.env');
        }
    }

    return Config::fromEnv($projectRootPath);
}
