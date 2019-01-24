<?php

namespace HireInSocial;

use HireInSocial\Application\System;
use HireInSocial\UserInterface\Symfony\SymfonyKernel;

function symfony(Config $config, System $system) : SymfonyKernel
{
    $frameworkConfig = [
        'framework' => [
            'secret' => $config->getString(Config::SYMFONY_SECRET),
            'csrf_protection' => null,
            'validation' => [
                'enabled' => true,
            ],
            'session' => [
                'cookie_samesite' => 'strict',
                'save_path' => '/var/lib/php/sessions',
            ],
            'default_locale' => $config->getString(Config::LOCALE),
            'translator' => [
                'fallbacks' => [$config->getString(Config::LOCALE)],
            ],
        ],
        'twig' => [
            'paths' => [
                $config->getString(Config::ROOT_PATH) . '/resources/templates/' . $config->getString(Config::LOCALE) . '/ui' => '__main__',
            ],
            'cache' => $config->getString(Config::ROOT_PATH) . '/var/cache/' . $config->getString(Config::ENV) . '/twig',
            'globals' => [
                'facebook' => [
                    'app_id' => $config->getString(Config::FB_APP_ID),
                    'group_id' => $config->getString(Config::FB_GROUP_ID),
                ],
            ],
            'auto_reload' => $config->getString(Config::ENV) !== 'prod',
        ],
        'monolog' => [
            'handlers' => [
                'file_log' => [
                    'type'  => 'stream',
                    'path'  => '%kernel.logs_dir%/%kernel.environment%_symfony.log',
                    'level' => 'debug',
                    'channels' => [
                        '!event', '!console', '!request', '!security',
                    ],
                ],
            ],
        ],
        'facebook' => [
            'app_id' => $config->getString(Config::FB_APP_ID),
            'app_secret' => $config->getString(Config::FB_APP_SECRET),
        ],
    ];

    if ($config->getString(Config::ENV) === 'test') {
        $frameworkConfig['framework']['test'] = true;
        $frameworkConfig['framework']['session']['storage_id'] = 'session.storage.mock_file';
    }

    return new SymfonyKernel(
        $config->getString(Config::ROOT_PATH),
        $config->getString(Config::ENV),
        $config->getString(Config::ENV) !== 'prod',
        $frameworkConfig,
        $system
    );
}
