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

use HireInSocial\Config;
use HireInSocial\HireInSocial;

function symfony(HireInSocial $hireInSocial) : SymfonyKernel
{
    $frameworkConfig = [
        'parameters' => [
            'google_recaptcha_secret' => $hireInSocial->config()->getString(Config::RECAPTCHA_SECRET),
            'apply_email_template' => $hireInSocial->config()->getString(Config::APPLY_EMAIL_TEMPLATE),
            'his.old_offer_days' => $hireInSocial->config()->getInt(Config::OLD_OFFER_DAYS),
        ],
        'framework' => [
            'secret' => $hireInSocial->config()->getString(Config::SYMFONY_SECRET),
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
            'default_locale' => $hireInSocial->config()->getString(Config::LOCALE),
            'translator' => [
                'fallbacks' => [$hireInSocial->config()->getString(Config::LOCALE)],
                'paths' => [
                    $hireInSocial->config()->getString(Config::ROOT_PATH) . '/resources/translations',
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
                $hireInSocial->config()->getString(Config::ROOT_PATH) . '/resources/templates/' . $hireInSocial->config()->getString(Config::LOCALE) . '/ui/offers' => 'offers',
            ],
            'default_path' => $hireInSocial->config()->getString(Config::ROOT_PATH) . '/resources/templates',
            'date' => [
                'timezone' => $hireInSocial->config()->getString(Config::TIMEZONE),
            ],
            'cache' => $hireInSocial->config()->getString(Config::ROOT_PATH) . '/var/cache/' . $hireInSocial->config()->getString(Config::ENV) . '/twig',
            'globals' => [
                'apply_email_template' => $hireInSocial->config()->getString(Config::APPLY_EMAIL_TEMPLATE),
                'facebook' => [
                    'app_id' => $hireInSocial->config()->getString(Config::FB_APP_ID),
                    'page_url' => $hireInSocial->config()->getString(Config::FB_PAGE_URL),
                ],
                'google' => [
                    'recaptcha' => [
                        'key' => $hireInSocial->config()->getString(Config::RECAPTCHA_KEY),
                    ],
                    'maps' => [
                        'key' => $hireInSocial->config()->getString(Config::GOOGLE_MAPS_KEY),
                    ],
                    'analytics' => [
                        'code' => $hireInSocial->config()->getString(Config::GOOGLE_ANALYTICS_CODE),
                    ],
                ],
                'assets' => [
                    'storage_url' => $hireInSocial->config()->getJson(Config::FILESYSTEM_CONFIG)['storage_url'],
                ],
                'contact_email' => $hireInSocial->config()->getString(Config::CONTACT_EMAIL),
                'his' => [
                    'old_offer_days' => $hireInSocial->config()->getInt(Config::OLD_OFFER_DAYS),
                    'domain' => $hireInSocial->config()->getString(Config::DOMAIN),
                ],
            ],
            'auto_reload' => $hireInSocial->config()->getString(Config::ENV) !== 'prod',
            'debug' => $hireInSocial->config()->getString(Config::ENV) !== 'prod',
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
            'app_id' => $hireInSocial->config()->getString(Config::FB_APP_ID),
            'app_secret' => $hireInSocial->config()->getString(Config::FB_APP_SECRET),
        ],
        'linkedin' => [
            'app_id' => $hireInSocial->config()->getString(Config::LINKEDIN_APP_ID),
            'app_secret' => $hireInSocial->config()->getString(Config::LINKEDIN_APP_SECRET),
        ],
    ];

    if ($hireInSocial->config()->getString(Config::ENV) === 'test') {
        $frameworkConfig['framework']['test'] = true;
        $frameworkConfig['framework']['session']['storage_id'] = 'session.storage.mock_file';
    }

    return new SymfonyKernel(
        $hireInSocial->config()->getString(Config::ROOT_PATH),
        $hireInSocial->config()->getString(Config::ENV),
        $hireInSocial->config()->getString(Config::ENV) !== 'prod',
        $frameworkConfig,
        $hireInSocial->offers()
    );
}
