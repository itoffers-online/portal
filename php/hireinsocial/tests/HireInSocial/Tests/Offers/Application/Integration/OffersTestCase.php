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

use HireInSocial\Config;
use HireInSocial\HireInSocial;
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
        $hireInSocial = new HireInSocial(ROOT_DIR);

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
