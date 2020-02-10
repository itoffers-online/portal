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

use HireInSocial\Component\CQRS\System\Query;
use HireInSocial\Offers\Application\Query\Twitter\Model\Tweet;

interface TweetsQuery extends Query
{
    public function findTweet(string $offerId) : ?Tweet;
}
