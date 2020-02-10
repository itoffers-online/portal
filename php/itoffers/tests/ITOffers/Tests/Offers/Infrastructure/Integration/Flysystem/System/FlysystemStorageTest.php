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

namespace ITOffers\Tests\Offers\Infrastructure\Integration\Flysystem\System;

use ITOffers\Offers\Infrastructure\Flysystem\Application\System\FlysystemStorage;
use PHPUnit\Framework\TestCase;

final class FlysystemStorageTest extends TestCase
{
    public function test_local_filesystem() : void
    {
        $filesystem = FlysystemStorage::create([
            'type' => 'local',
            'storage_url' => '/tmp',
            'local_storage_path' => __DIR__,
        ]);

        $this->assertEquals('local', $filesystem->config()['type']);
    }
}
