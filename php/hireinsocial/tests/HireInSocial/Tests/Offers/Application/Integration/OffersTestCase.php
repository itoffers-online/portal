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

namespace HireInSocial\Tests\Offers\Application\Integration;

use function App\symfony;
use HireInSocial\Config;
use HireInSocial\HireInSocial;
use function HireInSocial\Offers\Infrastructure\bootstrap;
use HireInSocial\Offers\Infrastructure\Flysystem\Application\System\FlysystemStorage;
use HireInSocial\Tests\Offers\Application\Context\DatabaseContext;
use HireInSocial\Tests\Offers\Application\Context\FilesystemContext;
use HireInSocial\Tests\Offers\Application\Context\OffersContext;
use PHPUnit\Framework\TestCase;

abstract class OffersTestCase extends TestCase
{
    /**
     * @var OffersContext
     */
    protected $systemContext;

    /**
     * @var DatabaseContext
     */
    protected $databaseContext;

    /**
     * @var FilesystemContext
     */
    protected $filesystemContext;

    public function setUp() : void
    {
        /**
         * This is nasty dependency I have no idea how to remove. On the one hand HireInSocial requires Twig, Monolog,
         * SwiftMailer dependencies that are also required by Symfony Framework (for the UI). On the other hand
         * creating them without symfony duplicates configuration.
         * Ideally I would like to create all those dependencies even out of symfony, pass them to the framework
         * and to HireInSocial when needed but it would increase the complexity of the project,
         * in this case it's justified to keep this dependency here.
         */
        $symfony = symfony(bootstrap(ROOT_DIR));

        $hireInSocial = $symfony->getContainer()->get(HireInSocial::class);

        if ($hireInSocial->config()->getString(Config::ENV) !== 'test') {
            $this->fail(sprintf('Expected environment "test" but got "%s"', $hireInSocial->config()->getString(Config::ENV)));
        }

        $this->systemContext = new OffersContext($hireInSocial->offers());
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
