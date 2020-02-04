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

namespace App\Tests\Functional\Web;

use function App\symfony;
use App\SymfonyKernel;
use HireInSocial\Config;
use HireInSocial\HireInSocial;
use HireInSocial\Offers\Offers;
use HireInSocial\Tests\Offers\Application\Context\DatabaseContext;
use HireInSocial\Tests\Offers\Application\Context\OffersContext;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class SymfonyKernelTestCase extends KernelTestCase
{
    /**
     * @var HireInSocial
     */
    protected static $hireInSocial;

    /**
     * @var OffersContext
     */
    protected $offersContext;

    /**
     * @var DatabaseContext
     */
    protected $databaseContext;

    protected static function getKernelClass()
    {
        return SymfonyKernel::class;
    }

    protected static function createKernel(array $options = [])
    {
        return symfony(static::hireInSocial());
    }

    protected static function hireInSocial() : HireInSocial
    {
        if (null === static::$hireInSocial) {
            static::$hireInSocial = new HireInSocial(ROOT_DIR);
        }

        if (static::$hireInSocial->config()->getString(Config::ENV) !== 'test') {
            throw new \RuntimeException(sprintf('Expected environment "test" but got "%s"', static::$hireInSocial->config()->getString(Config::ENV)));
        }

        return static::$hireInSocial;
    }

    protected static function offersFacade() : Offers
    {
        return static::hireInSocial()->offers();
    }

    public function setUp() : void
    {
        $this->offersContext = new OffersContext(static::offersFacade());
        $this->databaseContext = new DatabaseContext(static::hireInSocial()->dbal());

        $this->databaseContext->purgeDatabase();
    }
}
