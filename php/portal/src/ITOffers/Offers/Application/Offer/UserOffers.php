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

namespace ITOffers\Offers\Application\Offer;

use ITOffers\Offers\Application\User\User;

final class UserOffers implements \Countable
{
    private User $user;

    private \DateTimeImmutable $since;

    /**
     * @var Offer[]
     */
    private array $offers;

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
