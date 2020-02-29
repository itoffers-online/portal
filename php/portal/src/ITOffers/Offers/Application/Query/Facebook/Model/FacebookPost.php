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

namespace ITOffers\Offers\Application\Query\Facebook\Model;

use Ramsey\Uuid\UuidInterface;

final class FacebookPost
{
    private string $id;

    private UuidInterface $offerId;

    public function __construct(string $id, UuidInterface $offerId)
    {
        $this->id = $id;
        $this->offerId = $offerId;
    }

    /**
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * @return UuidInterface
     */
    public function getOfferId() : UuidInterface
    {
        return $this->offerId;
    }
}
