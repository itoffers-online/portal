<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Integration\Command\Throttle;

use HireInSocial\Application\Query\Offer\OfferThrottleQuery;
use HireInSocial\Tests\Application\Integration\HireInSocialTestCase;

final class RemoteThrottleTest extends HireInSocialTestCase
{
    public function test_removing_throttle_from_job_offer_author()
    {
        $user = $this->systemContext->createUser();

        $this->systemContext->createSpecialization($specialization = 'spec');
        $this->systemContext->postOffer($user->id(), $specialization);

        $this->assertTrue($this->systemContext->system()->query(OfferThrottleQuery::class)->isThrottled($user->id()));

        $this->systemContext->removeThrottle($user->id());

        $this->assertFalse($this->systemContext->system()->query(OfferThrottleQuery::class)->isThrottled($user->id()));
    }
}
