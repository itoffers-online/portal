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

namespace ITOffers\Tests\Offers\Application\Integration\Command\Specialization;

use ITOffers\Offers\Application\Command\Specialization\CreateSpecialization;
use ITOffers\Offers\Application\Command\Specialization\SetTwitterChannel;
use ITOffers\Offers\Application\Query\Specialization\Model\Specialization\TwitterChannel;
use ITOffers\Tests\Offers\Application\Integration\OffersTestCase;

final class SetTwitterChannelTest extends OffersTestCase
{
    public function test_set_specialization_twitter_channel() : void
    {
        $slug = 'php';

        $this->systemContext->offersFacade()->handle(new CreateSpecialization($slug));
        $this->systemContext->offersFacade()->handle(new SetTwitterChannel($slug, 'twitter_id', 'screen_name', 'token', 'secret'));

        $this->assertEquals(
            new TwitterChannel(
                'twitter_id',
                'screen_name'
            ),
            $this->systemContext->offersFacade()->specializationQuery()->findBySlug($slug)->twitterChannel()
        );
    }
}
