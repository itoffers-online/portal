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

namespace ITOffers\Tests\Offers\Application\Integration\Command\Twitter;

use ITOffers\Offers\Application\Command\Specialization\SetTwitterChannel;
use ITOffers\Offers\Application\Command\Twitter\TweetAboutOffer;
use ITOffers\Offers\Application\Query\Twitter\Model\Tweet;
use ITOffers\Tests\Offers\Application\Integration\OffersTestCase;

final class TweetAboutOfferTest extends OffersTestCase
{
    public function test_tweeting_about_offer() : void
    {
        $user = $this->offers->createUser();
        $specialization = $this->offers->createSpecialization($specialization = 'spec');
        $offer = $this->offers->createOffer($user->id(), $specialization->slug());
        $this->offers->module()->handle(new SetTwitterChannel(
            $specialization->slug(),
            'account_id',
            'account',
            'token',
            'secret',
        ));

        $this->offers->module()->handle(new TweetAboutOffer($offer->id()->toString(), 'This is offer message for facebook'));

        $this->assertInstanceOf(Tweet::class, $this->offers->module()->tweetsQuery()->findTweet($offer->id()->toString()));
    }
}
