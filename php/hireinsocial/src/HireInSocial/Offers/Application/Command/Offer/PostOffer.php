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

namespace HireInSocial\Offers\Application\Command\Offer;

use HireInSocial\Offers\Application\Command\ClassCommand;
use HireInSocial\Offers\Application\Command\Offer\Offer\Offer;
use HireInSocial\Offers\Application\System\Command;

final class PostOffer implements Command
{
    use ClassCommand;

    /**
     * @var string
     */
    private $specialization;

    /**
     * @var string
     */
    private $userId;

    /**
     * @var Offer
     */
    private $offer;

    /**
     * @var string|null
     */
    private $offerPDFPath;

    public function __construct(
        string $specialization,
        string $userId,
        Offer $offer,
        ?string $offerPDFPath = null
    ) {
        $this->userId = $userId;
        $this->offer = $offer;
        $this->specialization = $specialization;
        $this->offerPDFPath = $offerPDFPath;
    }

    public function specialization() : string
    {
        return $this->specialization;
    }

    public function userId() : string
    {
        return $this->userId;
    }

    public function offer() : Offer
    {
        return $this->offer;
    }

    public function offerPDFPath() : ?string
    {
        return $this->offerPDFPath;
    }
}
