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

namespace ITOffers;

use function \Safe\json_decode;
use function array_key_exists;
use RuntimeException;
use Safe\Exceptions\JsonException;

final class Config
{
    public const ENV = 'env';

    public const ROOT_PATH = 'root_path';

    public const CACHE_PATH = 'cache_path';

    public const LOGS_PATH = 'logs_path';

    public const DOMAIN = 'domain';

    public const CONTACT_EMAIL = 'contact_email';

    public const REPORT_EMAIL = 'report_email';

    public const LOCALE = 'locale';

    public const TIMEZONE = 'timezone';

    public const SYMFONY_SECRET = 'symfony_secret';

    public const APPLY_EMAIL_TEMPLATE = 'apply_email_template';

    public const APPLY_EMAIL_CONFIG = 'apply_email_config';

    public const MAILER_CONFIG = 'mailer_config';

    public const FILESYSTEM_CONFIG = 'filesystem_config';

    public const RECAPTCHA_KEY = 'recaptcha_key';

    public const RECAPTCHA_SECRET = 'recaptcha_secret';

    public const GOOGLE_MAPS_KEY = 'google_maps_key';

    public const GOOGLE_ANALYTICS_CODE = 'google_analytics_code';

    public const MAP_TILER_API_KEY = 'map_tiler_key';

    public const DB_HOST = 'db_host';

    public const DB_PORT = 'db_port';

    public const DB_USER = 'db_user';

    public const DB_USER_PASS = 'db_user_pass';

    public const DB_NAME = 'db_name';

    public const REDIS_DSN = 'redis_dsn';

    public const REDIS_DB_DOCTRINE_CACHE = 'REDIS_DB_DOCTRINE_CACHE';

    public const FB_PAGE_URL = 'fb_page_url';

    public const FB_APP_ID = 'facebook_app_id';

    public const FB_APP_SECRET = 'facebook_app_secret';

    public const FB_INTERNAL_APP_ID = 'facebook_internal_app_id';

    public const FB_INTERNAL_APP_SECRET = 'facebook_internal_app_secret';

    public const LINKEDIN_APP_ID = 'linkedin_app_id';

    public const LINKEDIN_APP_SECRET = 'linkedin_app_secret';

    public const OFFER_LIFETIME_DAYS = 'offer_lifetime_days';

    public const TWITTER_API_KEY = 'twitter_api_key';

    public const TWITTER_API_SECRET_KEY = 'twitter_api_secret_key';

    public const TWITTER_ACCESS_TOKEN = 'twitter_access_token';

    public const TWITTER_ACCESS_TOKEN_SECRET = 'twitter_access_token_secret';

    public const NOTIFICATIONS_DISABLED = 'notifications_disabled';

    public const FEATURE_POST_NEW_OFFERS = 'feature_post_new_offers';

    public const FEATURE_POST_OFFER_AT_FACEBOOK = 'feature_post_offer_at_facebook';

    public const FEATURE_TWEET_ABOUT_OFFER = 'feature_tweet_about_offer';

    private array $config;

    private function __construct(array $config)
    {
        $this->config = $config;
    }

    public static function fromEnv(string $projectRootPath) : self
    {
        return new self([
            self::ROOT_PATH => $projectRootPath,
            self::CACHE_PATH => getenv('ITOF_CACHE_PATH'),
            self::LOGS_PATH => getenv('ITOF_LOGS_PATH'),
            self::ENV => getenv('ITOF_ENV'),
            self::LOCALE => getenv('ITOF_LOCALE'),
            self::TIMEZONE => getenv('ITOF_TIMEZONE'),
            self::DOMAIN => getenv('ITOF_DOMAIN'),
            self::CONTACT_EMAIL => 'contact@' . getenv('ITOF_DOMAIN'),
            self::REPORT_EMAIL => 'report@' . getenv('ITOF_DOMAIN'),
            self::FB_PAGE_URL => 'https://www.facebook.com/itoffers.online/',
            self::SYMFONY_SECRET => getenv('ITOF_SYMFONY_SECRET'),
            self::REDIS_DSN => getenv('ITOF_REDIS_DSN'),
            self::APPLY_EMAIL_TEMPLATE => getenv('ITOF_APPLY_EMAIL_TEMPLATE'),
            self::APPLY_EMAIL_CONFIG => getenv('ITOF_APPLY_EMAIL_CONFIG'),
            self::RECAPTCHA_KEY => getenv('ITOF_RECAPTCHA_KEY'),
            self::RECAPTCHA_SECRET => getenv('ITOF_RECAPTCHA_SECRET'),
            self::GOOGLE_MAPS_KEY => getenv('ITOF_GOOGLE_MAPS_API_KEY'),
            self::GOOGLE_ANALYTICS_CODE => getenv('ITOF_GOOGLE_ANALYTICS_CODE'),
            self::MAP_TILER_API_KEY => getenv('ITOF_MAP_TILER_API_KEY'),
            self::MAILER_CONFIG => getenv('ITOF_MAILER_CONFIG'),
            self::FB_APP_ID => getenv('ITOF_FB_APP_ID'),
            self::FB_APP_SECRET => getenv('ITOF_FB_APP_SECRET'),
            self::FB_INTERNAL_APP_ID => getenv('ITOF_FB_INTERNAL_APP_ID'),
            self::FB_INTERNAL_APP_SECRET => getenv('ITOF_FB_INTERNAL_APP_SECRET'),
            self::LINKEDIN_APP_ID => getenv('ITOF_LINKEDIN_APP_ID'),
            self::LINKEDIN_APP_SECRET => getenv('ITOF_LINKEDIN_APP_SECRET'),
            self::FILESYSTEM_CONFIG => getenv('ITOF_FILESYSTEM_CONFIG'),
            self::DB_HOST => getenv('ITOF_DB_HOST'),
            self::DB_PORT => getenv('ITOF_DB_PORT'),
            self::DB_USER => getenv('ITOF_DB_USER'),
            self::DB_USER_PASS => getenv('ITOF_DB_USER_PASS'),
            self::DB_NAME => getenv('ITOF_DB_NAME'),
            self::REDIS_DB_DOCTRINE_CACHE => 2,
            self::OFFER_LIFETIME_DAYS => 20,
            self::TWITTER_API_KEY => getenv('ITOF_TWITTER_API_KEY'),
            self::TWITTER_API_SECRET_KEY => getenv('ITOF_TWITTER_API_SECRET_KEY'),
            self::TWITTER_ACCESS_TOKEN => getenv('ITOF_TWITTER_ACCESS_TOKEN'),
            self::TWITTER_ACCESS_TOKEN_SECRET => getenv('ITOF_TWITTER_ACCESS_TOKEN_SECRET'),
            self::NOTIFICATIONS_DISABLED => \filter_var(getenv('ITOF_NOTIFICATIONS_DISABLED'), FILTER_VALIDATE_BOOLEAN),
            self::FEATURE_POST_NEW_OFFERS => \filter_var(getenv('ITOF_FEATURE_POST_NEW_OFFERS'), FILTER_VALIDATE_BOOLEAN),
            self::FEATURE_POST_OFFER_AT_FACEBOOK => \filter_var(getenv('ITOF_FEATURE_POST_OFFER_AT_FACEBOOK'), FILTER_VALIDATE_BOOLEAN),
            self::FEATURE_TWEET_ABOUT_OFFER => \filter_var(getenv('ITOF_FEATURE_TWEET_ABOUT_OFFER'), FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function getString(string $key) : string
    {
        if (!$this->has($key)) {
            throw new RuntimeException(sprintf('Missing config key: %s', $key));
        }

        return (string) $this->config[$key];
    }

    public function getBool(string $key) : bool
    {
        if (!$this->has($key)) {
            throw new RuntimeException(sprintf('Missing config key: %s', $key));
        }

        return (bool) $this->config[$key];
    }

    /**
     * @throws JsonException
     */
    public function getJson(string $key) : array
    {
        return json_decode($this->getString($key), true);
    }

    public function getInt(string $key) : int
    {
        if (!$this->has($key)) {
            throw new RuntimeException(sprintf('Missing config key: %s', $key));
        }

        return (int) $this->config[$key];
    }

    public function override(string $key, string $value) : void
    {
        if (!$this->has($key)) {
            throw new RuntimeException(sprintf('Missing config key: %s', $key));
        }

        $this->config[$key] = $value;
    }

    private function has(string $key) : bool
    {
        return array_key_exists($key, $this->config);
    }
}
