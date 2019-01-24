<?php

namespace HireInSocial;

use Symfony\Component\Dotenv\Dotenv;

function bootstrap(string $projectRootPath) : Config
{
    if (!\file_exists($projectRootPath)) {
        die(sprintf('Invalid project root path: %s', $projectRootPath));
    }

    if (getenv('HIS_ENV') === 'test') {
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
