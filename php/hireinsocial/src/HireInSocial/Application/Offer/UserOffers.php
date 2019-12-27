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

namespace HireInSocial\Application\Offer;

use HireInSocial\Application\User\User;

final class UserOffers implements \Countable
{
    /**
     * @var \HireInSocial\Application\User\User
     */
    private $user;

    /**
     * @var \DateTimeImmutable
     */
    private $since;

    /**
     * @var \HireInSocial\Application\Offer\Offer[]
     */
    private $offers;

    public function __construct(User $user, \DateTimeImmutable $since, Offer ...$offers)
    {
        $this->user = $user;
        $this->since = $since;
        $this->offers = $offers;
    }

    public function count() : int
    {
        return \count($this->offers);
    }
}
