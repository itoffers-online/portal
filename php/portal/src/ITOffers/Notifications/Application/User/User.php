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

namespace ITOffers\Notifications\Application\User;

final class User
{
    private string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function email() : string
    {
        return $this->email;
    }
}
