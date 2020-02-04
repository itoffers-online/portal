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

namespace HireInSocial;

use App\Offers\Twig\Extension\TwigOfferExtension;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\PredisCache;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\Proxy\ProxyFactory;
use function HireInSocial\Notifications\Infrastructure\notificationsFacade;
use HireInSocial\Notifications\Notifications;
use HireInSocial\Offers\Infrastructure\Doctrine\DBAL\Platform\PostgreSQL11Platform;
use HireInSocial\Offers\Infrastructure\Doctrine\DBAL\Types\Offer\Description\Requirements\SkillsType;
use HireInSocial\Offers\Infrastructure\Doctrine\DBAL\Types\Offer\SalaryType;
use function HireInSocial\Offers\Infrastructure\offersFacade;
use HireInSocial\Offers\Offers;
use HireInSocial\Offers\UserInterface\OfferExtension;
use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\Dotenv\Dotenv;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class HireInSocial
{
    /**
     * @var string
     */
    private $projectRootPath;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Offers
     */
    private $offers;

    /**
     * @var Notifications
     */
    private $notifications;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var EntityManager
     */
    private $orm;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $templatingEngine;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(string $projectRootPath)
    {
        if (!\file_exists($projectRootPath)) {
            die(sprintf('Invalid project root path: %s', $projectRootPath));
        }

        $this->projectRootPath = $projectRootPath;
    }

    /**
     * Offers Module
     */
    public function offers() : Offers
    {
        if (null === $this->offers) {
            $this->offers = offersFacade($this->config(), $this->orm(), $this->mailer(), $this->templatingEngine(), $this->logger());
        }

        return $this->offers;
    }

    /**
     * Notifications Module
     */
    public function notifications() : Notifications
    {
        if (null === $this->notifications) {
            $this->notifications = notificationsFacade($this->config());
        }

        return $this->notifications;
    }

    public function config() : Config
    {
        if (null === $this->config) {
            if (\getenv('HIS_ENV') === 'test') {
                $dotEnv = new Dotenv();
                $dotEnv->load($this->projectRootPath . '/.env.test');
            } else {
                if (\file_exists($this->projectRootPath . '/.env')) {
                    $dotEnv = new Dotenv();
                    $dotEnv->load($this->projectRootPath . '/.env');
                }
            }

            $this->config = Config::fromEnv($this->projectRootPath);
        }

        return $this->config;
    }

    public function isDevMode() : bool
    {
        return $this->config()->getString(Config::ENV) !== 'prod';
    }

    public function isTestEnvironment() : bool
    {
        return $this->config()->getString(Config::ENV) === 'test';
    }

    public function isProdEnvironment() : bool
    {
        return $this->config()->getString(Config::ENV) === 'prod';
    }

    public function logger() : LoggerInterface
    {
        if (null !== $this->logger) {
            return $this->logger;
        }

        $logDir = $this->config()->getString(Config::ROOT_PATH) . '/var/logs';

        $this->logger = new Logger('system');
        $this->logger->pushHandler(new StreamHandler($logDir . sprintf('/%s_system.log', $this->config()->getString(Config::ENV)), Logger::DEBUG));

        if ($this->isProdEnvironment()) {
            ErrorHandler::register($this->logger);
        }

        return $this->logger;
    }

    public function dbal() : Connection
    {
        if (null !== $this->connection) {
            return $this->connection;
        }

        if (!Type::hasType(SalaryType::NAME)) {
            Type::addType(SalaryType::NAME, SalaryType::class);
        }
        if (!Type::hasType(SkillsType::NAME)) {
            Type::addType(SkillsType::NAME, SkillsType::class);
        }

        $this->connection = DriverManager::getConnection(
            [
                'dbname' => $this->config()->getString(Config::DB_NAME),
                'user' => $this->config()->getString(Config::DB_USER),
                'password' => $this->config()->getString(Config::DB_USER_PASS),
                'host' => $this->config()->getString(Config::DB_HOST),
                'port' => $this->config()->getInt(Config::DB_PORT),
                'driver' => 'pdo_pgsql',
                'platform' => new PostgreSQL11Platform(),
            ],
            new Configuration()
        );

        return $this->connection;
    }

    public function orm() : EntityManager
    {
        if (null !== $this->orm) {
            return $this->orm;
        }
        $configuration = new \Doctrine\ORM\Configuration();

        $configuration->setMetadataDriverImpl(new SimplifiedXmlDriver(
            [
                $this->config()->getString(Config::ROOT_PATH) . '/db/orm/mapping/xml' => 'HireInSocial\Offers\Application',
            ]
        ));

        $configuration->setNamingStrategy(new UnderscoreNamingStrategy(CASE_LOWER));

        if ($this->isDevMode()) {
            $cache = new ArrayCache;
        } else {
            $cache = new PredisCache(new Client($this->config()->getString(Config::REDIS_DSN) . '/' . $this->config()->getInt(Config::REDIS_DB_DOCTRINE_CACHE)));
        }

        $configuration->setMetadataCacheImpl($cache);
        $configuration->setQueryCacheImpl($cache);

        $configuration->setProxyDir($this->config()->getString(Config::ROOT_PATH) . '/var/cache/orm');
        $configuration->setProxyNamespace('DoctrineProxy');
        $configuration->setAutoGenerateProxyClasses($this->isDevMode());

        if ($this->isDevMode()) {
            $configuration->setAutoGenerateProxyClasses(ProxyFactory::AUTOGENERATE_EVAL);
        }

        $this->orm = EntityManager::create($this->dbal(), $configuration);

        return $this->orm;
    }

    private function mailer() : \Swift_Mailer
    {
        if (null !== $this->mailer) {
            return $this->mailer;
        }

        if ($this->isTestEnvironment()) {
            $transport = new \Swift_Transport_NullTransport(new \Swift_Events_SimpleEventDispatcher());

            $this->mailer = new \Swift_Mailer($transport);

            return $this->mailer;
        }

        $transport = (new \Swift_SmtpTransport(
            $this->config()->getJson(Config::MAILER_CONFIG)['host'],
            $this->config()->getJson(Config::MAILER_CONFIG)['port']
        ))
            ->setUsername($this->config()->getJson(Config::MAILER_CONFIG)['username'])
            ->setPassword($this->config()->getJson(Config::MAILER_CONFIG)['password'])
            ->setTimeout(10)
        ;
        $this->mailer = new \Swift_Mailer($transport);

        return $this->mailer;
    }

    private function templatingEngine() : Environment
    {
        if (null !== $this->templatingEngine) {
            return $this->templatingEngine;
        }

        $loader = new FilesystemLoader($this->config()->getString(Config::ROOT_PATH) . '/resources/templates/' . $this->config()->getString(Config::LOCALE));
        $this->templatingEngine = new Environment($loader, [
            'cache' => $this->config()->getString(Config::ROOT_PATH) . '/var/cache/' . $this->config()->getString(Config::ENV) . '/twig',
            'debug' => $this->config()->getString(Config::ENV) !== 'prod',
            'auto_reload' => $this->isDevMode(),
        ]);
        $this->templatingEngine->addGlobal('apply_email_template', $this->config()->getString(Config::APPLY_EMAIL_TEMPLATE));
        $this->templatingEngine->addGlobal('domain', $this->config()->getString(Config::DOMAIN));

        $this->templatingEngine->addExtension(new TwigOfferExtension(new OfferExtension($this->config()->getString(Config::LOCALE))));

        return $this->templatingEngine;
    }
}
