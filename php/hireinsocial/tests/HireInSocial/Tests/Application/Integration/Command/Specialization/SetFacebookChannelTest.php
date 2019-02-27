<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Integration\Command\Specialization;

use HireInSocial\Application\Command\Specialization\CreateSpecialization;
use HireInSocial\Application\Command\Specialization\SetFacebookChannel;
use HireInSocial\Application\Query\Specialization\Model\Specialization\FacebookChannel;
use HireInSocial\Application\Query\Specialization\SpecializationQuery;
use HireInSocial\Tests\Application\Integration\HireInSocialTestCase;

final class SetFacebookChannelTest extends HireInSocialTestCase
{
    public function test_set_specilaization_facebook_channel()
    {
        $slug = 'php';

        $this->systemContext->system()->handle(new CreateSpecialization($slug));
        $this->systemContext->system()->handle(new SetFacebookChannel($slug, 'fb_page_id', 'fb_page_token', 'fb_group_id'));

        $this->assertEquals(
            new FacebookChannel(
                'fb_page_id',
                'fb_group_id'
            ),
            $this->systemContext->system()->query(SpecializationQuery::class)->findBySlug($slug)->facebookChannel()
        );
    }
}
