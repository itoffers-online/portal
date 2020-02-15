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

namespace ITOffers\Component\Storage;

use ITOffers\Component\Storage\FileStorage\File;

interface FileStorage
{
    public function upload(File $file) : void;

    public function purge() : void;
}