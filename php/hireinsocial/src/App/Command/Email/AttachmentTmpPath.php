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

namespace App\Command\Email;

final class AttachmentTmpPath
{
    private $basePath;

    private $filename;

    public function __construct(string $basePath, string $filename)
    {
        $this->filename = $filename;
        $this->basePath = rtrim($basePath, '/');
    }

    public function toString() : string
    {
        return $this->basePath . '/' . $this->filename;
    }

    public function filename() : string
    {
        return $this->filename;
    }
}
