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

namespace HireInSocial\Offers\Application\Query\Offer\Model;

final class Offers extends \ArrayObject
{
    public function __construct(Offer ...$specializations)
    {
        parent::__construct($specializations);
    }

    public function first() : Offer
    {
        return \current((array) $this);
    }
}
