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

namespace HireInSocial\Tests\Offers\Application\Double\Dummy;

use HireInSocial\Offers\Application\Specialization\TwitterChannel;
use HireInSocial\Offers\Application\Twitter\Twitter;

final class DummyTwitter implements Twitter
{
    public function tweet(string $message, TwitterChannel $account) : string
    {
        return \md5(\uniqid('tweet_id'));
    }
}
