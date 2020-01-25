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

namespace HireInSocial\Offers\Infrastructure\Imagine\UserInterface;

use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\RGB;

final class ImagineColors
{
    /**
     * @var RGB
     */
    private $palette;

    public function __construct()
    {
        $this->palette = new RGB();
    }

    public function text() : ColorInterface
    {
        return $this->palette->color('#212529', 100);
    }

    public function dark() : ColorInterface
    {
        return $this->palette->color('#343a40', 100);
    }

    public function green() : ColorInterface
    {
        return $this->palette->color('#80f24b', 100);
    }

    public function gray() : ColorInterface
    {
        return $this->palette->color('#adb5bd', 100);
    }

    public function primary() : ColorInterface
    {
        return $this->palette->color('#007bff', 100);
    }

    public function blue() : ColorInterface
    {
        return $this->palette->color('#007bff', 100);
    }

    public function light() : ColorInterface
    {
        return $this->palette->color('#f8f9fa', 100);
    }

    public function white() : ColorInterface
    {
        return $this->palette->color('#ffffff', 100);
    }

    public function border() : ColorInterface
    {
        return $this->palette->color('#dee2e6', 100);
    }
}
