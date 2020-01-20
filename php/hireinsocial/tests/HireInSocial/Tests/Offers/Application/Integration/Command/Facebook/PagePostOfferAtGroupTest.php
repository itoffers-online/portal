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

namespace HireInSocial\Tests\Offers\Application\Integration\Command\Facebook;

use HireInSocial\Offers\Application\Command\Facebook\PagePostOfferAtGroup;
use HireInSocial\Offers\Application\Command\Specialization\SetFacebookChannel;
use HireInSocial\Offers\Application\Exception\Exception;
use HireInSocial\Offers\Application\Query\SocialChannel\Facebook\Model\FacebookPost;
use HireInSocial\Tests\Offers\Application\Integration\OffersTestCase;

final class PagePostOfferAtGroupTest extends OffersTestCase
{
    public function test_posting_offer_to_facebook_group() : void
    {
        $user = $this->systemContext->createUser();
        $specialization = $this->systemContext->createSpecialization($specialization = 'spec');
        $offer = $this->systemContext->createOffer($user->id(), $specialization->slug());
        $this->systemContext->offersFacade()->handle(new SetFacebookChannel(
            $specialization->slug(),
            'page_id',
            'page_token',
            'group_id',
        ));

        $this->systemContext->offersFacade()->handle(new PagePostOfferAtGroup($offer->id()->toString(), 'This is offer message for facebook'));

        $this->assertInstanceOf(FacebookPost::class, $this->systemContext->offersFacade()->facebookPostQuery()->findFacebookPost($offer->id()->toString()));
    }

    public function test_posting_offer_to_facebook_when_specialization_fb_channel_is_not_set() : void
    {
        $user = $this->systemContext->createUser();
        $specialization = $this->systemContext->createSpecialization($specialization = 'spec');
        $offer = $this->systemContext->createOffer($user->id(), $specialization->slug());

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Specialization "spec" does not have facebook channel assigned.');

        $this->systemContext->offersFacade()->handle(new PagePostOfferAtGroup($offer->id()->toString(), 'This is offer message for facebook'));
    }
}
