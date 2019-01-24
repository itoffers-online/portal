<?php

declare(strict_types=1);

namespace HireInSocial;

final class Config
{
    public const ROOT_PATH = 'root_path';
    public const ENV = 'env';
    public const LOCALE = 'locale';

    public const SYMFONY_SECRET = 'symfony_secret';

    public const REDIS_DSN = 'redis_dsn';

    public const DB_HOST = 'db_host';
    public const DB_PORT = 'db_port';
    public const DB_USER = 'db_user';
    public const DB_USER_PASS = 'db_user_pass';
    public const DB_NAME = 'db_name';

    public const FB_APP_ID = 'facebook_app_id';
    public const FB_APP_SECRET = 'facebook_app_secret';
    public const FB_PAGE_ID = 'facebook_page_id';
    public const FB_PAGE_TOKEN = 'facebook_page_token';
    public const FB_GROUP_ID = 'facebook_group_id';

    public const THROTTLE_DURATION = 'throttle_duration';

    private $config;

    private function __construct(array $config)
    {
        $this->config = $config;
    }

    public static function fromEnv(string $projectRootPath) : self
    {
        return new self([
            self::ROOT_PATH => $projectRootPath,
            self::ENV => getenv('HIS_ENV'),
            self::LOCALE => getenv('HIS_LOCALE'),
            self::SYMFONY_SECRET => getenv('HIS_SYMFONY_SECRET'),
            self::REDIS_DSN => getenv('HIS_REDIS_DSN'),
            self::FB_APP_ID => getenv('HIS_FB_APP_ID'),
            self::FB_APP_SECRET => getenv('HIS_FB_APP_SECRET'),
            self::FB_PAGE_ID => getenv('HIS_FB_PAGE_ID'),
            self::FB_PAGE_TOKEN => getenv('HIS_FB_PAGE_ACCESS_TOKEN'),
            self::FB_GROUP_ID => getenv('HIS_FB_GROUP_ID'),
            self::THROTTLE_DURATION => getenv('HIS_THROTTLE_DURATION'),
            self::DB_HOST => getenv('HIS_DB_HOST'),
            self::DB_PORT => getenv('HIS_DB_PORT'),
            self::DB_USER => getenv('HIS_DB_USER'),
            self::DB_USER_PASS => getenv('HIS_DB_USER_PASS'),
            self::DB_NAME => getenv('HIS_DB_NAME'),
        ]);
    }

    public function getString(string $key): string
    {
        $this->assertConfigKey($key);

        return (string) $this->config[$key];
    }

    public function getInt(string $key): int
    {
        $this->assertConfigKey($key);

        return (int) $this->config[$key];
    }

    public function override(string $key, string $value) : void
    {
        $this->assertConfigKey($key);

        $this->config[$key] = $value;
    }

    private function assertConfigKey(string $key): void
    {
        if (false === isset($this->config[$key])) {
        {
            throw new \RuntimeException(\sprintf('Missing config key: %s', $key));
        }
    }
}
