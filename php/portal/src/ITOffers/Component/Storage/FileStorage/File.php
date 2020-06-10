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

namespace ITOffers\Component\Storage\FileStorage;

use finfo;
use ITOffers\Offers\Application\Assertion;

final class File
{
    private string $destinationPath;

    private string $tmpPath;

    public function __construct(string $dstPath, string $tmpPath)
    {
        Assertion::notEmpty($dstPath);
        Assertion::file($tmpPath);

        $this->destinationPath = $dstPath;
        $this->tmpPath = $tmpPath;
    }

    public static function pdf(string $dstPath, string $tmpPath) : self
    {
        $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($tmpPath);
        Assertion::inArray($mimeType, ['application/pdf', 'application/x-pdf'], sprintf('Expected application/pdf file got %s', $mimeType));

        return new self($dstPath, $tmpPath);
    }

    public static function image(string $dstPath, string $tmpPath) : self
    {
        $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($tmpPath);
        Assertion::inArray($mimeType, ['image/jpeg', 'image/png'], sprintf('Expected png or jpeg file got %s', $mimeType));

        return new self($dstPath, $tmpPath);
    }

    public static function extension(string $path) : string
    {
        return (new finfo(FILEINFO_EXTENSION))->file($path);
    }

    public function destinationPath() : string
    {
        return $this->destinationPath;
    }

    public function tmpPath() : string
    {
        return $this->tmpPath;
    }
}
