<?php

declare (strict_types=1);

namespace HireInSocial\Tests\Application\Integration\Command\Facebook\Page;

use HireInSocial\Application\Query\Offer\OfferThrottleQuery;
use HireInSocial\Tests\Application\Integration\HireInSocialTestCase;

final class PostToGroupTest extends HireInSocialTestCase
{
    public function test_posting_to_facebook_group_as_a_page()
    {
        $this->systemContext->postToFacebookGroup('FB_USER_ID');

        $this->assertTrue($this->systemContext->system()->query(OfferThrottleQuery::class)->isThrottled('FB_USER_ID'));
    }
}