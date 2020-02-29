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

namespace ITOffers\Tests\Offers\Application\Integration;

use function App\symfony;
use ITOffers\Component\EventBus\Infrastructure\InMemory\InMemoryEventBus;
use ITOffers\Config;
use ITOffers\ITOffersOnline;
use function ITOffers\Offers\Infrastructure\bootstrap;
use ITOffers\Offers\Infrastructure\Flysystem\Application\System\FlysystemStorage;
use ITOffers\Tests\Offers\Application\Context\DatabaseContext;
use ITOffers\Tests\Offers\Application\Context\FilesystemContext;
use ITOffers\Tests\Offers\Application\Context\OffersContext;
use ITOffers\Tests\Offers\Application\Double\Spy\EventSubscriberSpy;
use PHPUnit\Framework\TestCase;

abstract class OffersTestCase extends TestCase
{
    protected OffersContext $offers;

    protected DatabaseContext $databaseContext;

    protected FilesystemContext $filesystemContext;

    protected EventSubscriberSpy $publishedEvents;

    public function setUp() : void
    {
        /**
         * This is nasty dependency I have no idea how to remove. On the one hand itoffers.online requires Twig, Monolog,
         * SwiftMailer dependencies that are also required by Symfony Framework (for the UI). On the other hand
         * creating them without symfony duplicates configuration.
         * Ideally I would like to create all those dependencies even out of symfony, pass them to the framework
         * and to itoffers.online when needed but it would increase the complexity of the project,
         * in this case it's justified to keep this dependency here.
         */
        $symfony = symfony(bootstrap(ROOT_DIR));
        $hireInSocial = $symfony->getContainer()->get(ITOffersOnline::class);

        // So we can assert that event was published to the topic
        $hireInSocial->eventBus()->registerTo(
            InMemoryEventBus::TOPIC_OFFERS,
            $this->publishedEvents = new EventSubscriberSpy()
        );

        if ($hireInSocial->config()->getString(Config::ENV) !== 'test') {
            $this->fail(sprintf('Expected environment "test" but got "%s"', $hireInSocial->config()->getString(Config::ENV)));
        }

        $this->offers = new OffersContext($hireInSocial->offers());
        $this->databaseContext = new DatabaseContext($hireInSocial->dbal());
        $this->filesystemContext = new FilesystemContext(FlysystemStorage::create($hireInSocial->config()->getJson(Config::FILESYSTEM_CONFIG)));

        $this->databaseContext->purgeDatabase();
        $this->filesystemContext->purgeFilesystem();
    }

    public function tearDown() : void
    {
        $this->filesystemContext->purgeFilesystem();
    }
}
