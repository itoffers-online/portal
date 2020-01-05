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

namespace App;

use HireInSocial\Offers\Application\Config;
use HireInSocial\Offers\Offers;

function symfony(Config $config, Offers $offers) : SymfonyKernel
{
    $frameworkConfig = [
        'parameters' => [
            'google_recaptcha_secret' => $config->getString(Config::RECAPTCHA_SECRET),
            'apply_email_template' => $config->getString(Config::APPLY_EMAIL_TEMPLATE),
        ],
        'framework' => [
            'secret' => $config->getString(Config::SYMFONY_SECRET),
            'csrf_protection' => null,
            'esi' => [
                'enabled' => true,
            ],
            'validation' => [
                'enabled' => true,
                'enable_annotations' => false,
            ],
            'annotations' => [
                'enabled' => false,
            ],
            'session' => [
                'cookie_samesite' => 'strict',
                'save_path' => sys_get_temp_dir() . '/his/sessions',
            ],
            'default_locale' => $config->getString(Config::LOCALE),
            'translator' => [
                'fallbacks' => [$config->getString(Config::LOCALE)],
                'paths' => [
                    $config->getString(Config::ROOT_PATH) . '/resources/translations',
                ],
            ],
            'templating' => [
                'engines' => [
                    'twig',
                ],
            ],
        ],
        'twig' => [
            'paths' => [
                $config->getString(Config::ROOT_PATH) . '/resources/templates/' . $config->getString(Config::LOCALE) . '/ui/offers' => 'offers',
            ],
            'date' => [
                'timezone' => $config->getString(Config::TIMEZONE),
            ],
            'cache' => $config->getString(Config::ROOT_PATH) . '/var/cache/' . $config->getString(Config::ENV) . '/twig',
            'globals' => [
                'apply_email_template' => $config->getString(Config::APPLY_EMAIL_TEMPLATE),
                'facebook' => [
                    'app_id' => $config->getString(Config::FB_APP_ID),
                    'page_url' => $config->getString(Config::FB_PAGE_URL),
                ],
                'google' => [
                    'recaptcha' => [
                        'key' => $config->getString(Config::RECAPTCHA_KEY),
                    ],
                    'maps' => [
                        'key' => $config->getString(Config::GOOGLE_MAPS_KEY),
                    ],
                ],
                'assets' => [
                    'storage_url' => $config->getJson(Config::FILESYSTEM_CONFIG)['storage_url'],
                ],
                'contact_email' => $config->getString(Config::CONTACT_EMAIL),
            ],
            'auto_reload' => $config->getString(Config::ENV) !== 'prod',
            'debug' => $config->getString(Config::ENV) !== 'prod',
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
        $offers
    );
}
