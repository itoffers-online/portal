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

namespace HireInSocial\Offers\Application\Twitter;

use HireInSocial\Offers\Application\Assertion;
use HireInSocial\Offers\Application\Offer\Offer;

class Tweet
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $jobOfferId;

    public function __construct(string $id, Offer $offer)
    {
        Assertion::notEmpty($id);

        $this->id = $id;
        $this->jobOfferId = $offer->id()->toString();
    }
}
