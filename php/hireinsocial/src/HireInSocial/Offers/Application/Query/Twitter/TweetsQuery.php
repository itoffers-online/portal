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

namespace HireInSocial\Offers\Application\Query\Twitter;

use HireInSocial\Offers\Application\Query\Twitter\Model\Tweet;
use HireInSocial\Offers\Application\System\Query;

interface TweetsQuery extends Query
{
    public function findTweet(string $offerId) : ?Tweet;
}
