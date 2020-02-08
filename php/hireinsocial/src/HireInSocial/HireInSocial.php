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
use HireInSocial\Component\EventBus\Infrastructure\InMemory\InMemoryEventBus;
use HireInSocial\Component\Mailer\Infrastructure\SwiftMailer\SwiftMailer;
use HireInSocial\Component\Mailer\Mailer;
use function HireInSocial\Notifications\Infrastructure\notificationsFacade;
use HireInSocial\Notifications\Notifications;
use HireInSocial\Offers\Infrastructure\Doctrine\DBAL\Platform\PostgreSQL11Platform;
use HireInSocial\Offers\Infrastructure\Doctrine\DBAL\Types\Offer\Description\Requirements\SkillsType;
use HireInSocial\Offers\Infrastructure\Doctrine\DBAL\Types\Offer\SalaryType;
use function HireInSocial\Offers\Infrastructure\offersFacade;
use HireInSocial\Offers\Offers;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Twig\Environment;

final class HireInSocial
{
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

    /**
     * @var InMemoryEventBus
     */
    private $eventBus;

    public function __construct(Config $config, LoggerInterface $logger, Environment $twig, \Swift_Mailer $mailer)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->templatingEngine = $twig;
        $this->mailer = $mailer;
    }

    /**
     * Offers Module
     */
    public function offers() : Offers
    {
        if (null === $this->offers) {
            $this->offers = offersFacade($this->config(), $this->orm(), $this->mailer(), $this->templatingEngine(), $this->eventBus(), $this->logger());
        }

        return $this->offers;
    }

    /**
     * Notifications Module
     */
    public function notifications() : Notifications
    {
        if (null === $this->notifications) {
            $this->notifications = notificationsFacade($this->config(), $this->eventBus(), $this->mailer(), $this->logger());
        }

        return $this->notifications;
    }

    public function config() : Config
    {
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

    private function mailer() : Mailer
    {
        return new SwiftMailer($this->config()->getString(Config::DOMAIN), $this->mailer);
    }

    private function templatingEngine() : Environment
    {
        return $this->templatingEngine;
    }

    private function eventBus() : InMemoryEventBus
    {
        if (null !== $this->eventBus) {
            return $this->eventBus;
        }

        $this->eventBus = new InMemoryEventBus();

        return $this->eventBus;
    }
}
