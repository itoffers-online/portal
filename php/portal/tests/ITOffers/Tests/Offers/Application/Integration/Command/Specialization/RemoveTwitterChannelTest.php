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
use ITOffers\Offers\Application\Command\Specialization\RemoveTwitterChannel;
use ITOffers\Offers\Application\Command\Specialization\SetTwitterChannel;
use ITOffers\Tests\Offers\Application\Integration\OffersTestCase;

final class RemoveTwitterChannelTest extends OffersTestCase
{
    public function test_remove_facebook_channel() : void
    {
        $slug = 'php';

        $this->systemContext->offersFacade()->handle(new CreateSpecialization($slug));
        $this->systemContext->offersFacade()->handle(new SetTwitterChannel($slug, 'twitter_id', 'screen_name', 'token', 'secret'));
        $this->systemContext->offersFacade()->handle(new RemoveTwitterChannel($slug));

        $this->assertNull(
            $this->systemContext->offersFacade()->specializationQuery()->findBySlug($slug)->twitterChannel()
        );
    }
}