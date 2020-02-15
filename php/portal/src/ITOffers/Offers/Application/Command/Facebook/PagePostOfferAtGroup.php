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

namespace ITOffers\Offers\Application\Command\Facebook;

use ITOffers\Component\CQRS\System\Command;
use ITOffers\Offers\Application\Command\ClassCommand;

final class PagePostOfferAtGroup implements Command
{
    use ClassCommand;

    /**
     * @var string
     */
    private $offerId;

    /**
     * @var string
     */
    private $message;

    public function __construct(string $offerId, string $message)
    {
        $this->message = $message;
        $this->offerId = $offerId;
    }

    public function offerId() : string
    {
        return $this->offerId;
    }

    public function message() : string
    {
        return $this->message;
    }
}
