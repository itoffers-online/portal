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

namespace HireInSocial\Tests\Offers\Application\Integration\Command\Specialization;

use HireInSocial\Offers\Application\Command\Specialization\CreateSpecialization;
use HireInSocial\Offers\Application\Command\Specialization\SetFacebookChannel;
use HireInSocial\Offers\Application\Query\Specialization\Model\Specialization\FacebookChannel;
use HireInSocial\Tests\Offers\Application\Integration\OffersTestCase;

final class SetFacebookChannelTest extends OffersTestCase
{
    public function test_set_specilaization_facebook_channel() : void
    {
        $slug = 'php';

        $this->systemContext->offersFacade()->handle(new CreateSpecialization($slug));
        $this->systemContext->offersFacade()->handle(new SetFacebookChannel($slug, 'fb_page_id', 'fb_page_token', 'fb_group_id', 'fb_group_name'));

        $this->assertEquals(
            new FacebookChannel(
                'fb_page_id',
                'fb_group_id'
            ),
            $this->systemContext->offersFacade()->specializationQuery()->findBySlug($slug)->facebookChannel()
        );
    }
}
