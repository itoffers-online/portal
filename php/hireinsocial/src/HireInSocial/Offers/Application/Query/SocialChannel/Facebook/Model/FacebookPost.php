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

namespace HireInSocial\Offers\Application\Query\SocialChannel\Facebook\Model;

use Ramsey\Uuid\UuidInterface;

class FacebookPost
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var UuidInterface
     */
    private $offerId;

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
