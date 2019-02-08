<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Integration\Command\Throttle;

use HireInSocial\Application\Query\Offer\OfferThrottleQuery;
use HireInSocial\Tests\Application\Integration\HireInSocialTestCase;

final class RemoteThrottleTest extends HireInSocialTestCase
{
    public function test_removing_throttle_from_job_offer_author()
    {
        $this->systemContext->postToFacebookGroup('FB_USER_ID');

        $this->assertTrue($this->systemContext->system()->query(OfferThrottleQuery::class)->isThrottled('FB_USER_ID'));

        $this->systemContext->removeThrottle('FB_USER_ID');

        $this->assertFalse($this->systemContext->system()->query(OfferThrottleQuery::class)->isThrottled('FB_USER_ID'));
    }
}
