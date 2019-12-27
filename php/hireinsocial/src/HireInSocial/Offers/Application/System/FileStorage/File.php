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

namespace HireInSocial\Offers\Application\System\FileStorage;

use HireInSocial\Offers\Application\Assertion;

final class File
{
    /**
     * @var string
     */
    private $destinationPath;

    /**
     * @var string
     */
    private $tmpPath;

    public function __construct(string $dstPath, string $tmpPath)
    {
        Assertion::notEmpty($dstPath);
        Assertion::file($tmpPath);

        $this->destinationPath = $dstPath;
        $this->tmpPath = $tmpPath;
    }

    public static function pdf(string $dstPath, string $tmpPath) : self
    {
        $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->buffer(\file_get_contents($tmpPath));
        Assertion::inArray($mimeType, ['application/pdf', 'application/x-pdf'], sprintf('Expected application/pdf file got %s', $mimeType));

        return new self($dstPath, $tmpPath);
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
