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

namespace HireInSocial\Tests\Offers\Application\Context;

use HireInSocial\Offers\Application\System\FileStorage;

final class FilesystemContext
{
    /**
     * @var FileStorage
     */
    private $fileStorage;

    public function __construct(FileStorage $fileStorage)
    {
        $this->fileStorage = $fileStorage;
    }

    public function purgeFilesystem() : void
    {
        $this->fileStorage->purge();
    }
}
