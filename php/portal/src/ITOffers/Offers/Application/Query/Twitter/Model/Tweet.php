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

namespace ITOffers\Offers\Application\Query\Twitter\Model;

use ITOffers\Offers\Application\Query\Specialization\Model\Specialization;

final class Tweet
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function url(Specialization $specialization) : string
    {
        return \sprintf('https://twitter.com/%s/status/%s', $specialization->twitterChannel()->screenName(), $this->id);
    }
}
