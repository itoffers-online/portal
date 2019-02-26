<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Integration\Command\Specialization;

use HireInSocial\Application\Command\Specialization\CreateSpecialization;
use HireInSocial\Application\Command\Specialization\RemoveFacebookChannel;
use HireInSocial\Application\Command\Specialization\SetFacebookChannel;
use HireInSocial\Application\Query\Specialization\SpecializationQuery;
use HireInSocial\Tests\Application\Integration\HireInSocialTestCase;

final class RemoveFacebookChannelTest extends HireInSocialTestCase
{
    public function test_remove_facebook_channel()
    {
        $slug = 'php';

        $this->systemContext->system()->handle(new CreateSpecialization($slug));
        $this->systemContext->system()->handle(new SetFacebookChannel($slug, 'fb_page_id', 'fb_page_token', 'fb_group_id'));
        $this->systemContext->system()->handle(new RemoveFacebookChannel($slug));

        $this->assertNull(
            $this->systemContext->system()->query(SpecializationQuery::class)->findBySlug($slug)->facebookChannel()
        );
    }
}
