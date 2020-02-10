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

namespace ITOffers\Offers\Application\Twitter;

use ITOffers\Offers\Application\Specialization\TwitterChannel;

interface Twitter
{
    /**
     * Returns tweet id
     */
    public function tweet(string $message, TwitterChannel $account) : string;
}
