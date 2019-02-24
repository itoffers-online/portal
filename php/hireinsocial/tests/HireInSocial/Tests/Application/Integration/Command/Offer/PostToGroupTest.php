<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Integration\Command\Offer;

use HireInSocial\Application\Query\Offer\OfferQuery;
use HireInSocial\Application\Query\Offer\OfferThrottleQuery;
use HireInSocial\Tests\Application\Integration\HireInSocialTestCase;

final class PostToGroupTest extends HireInSocialTestCase
{
    public function test_posting_offer()
    {
        $user = $this->systemContext->createUser();

        $this->systemContext->createSpecialization($specialization = 'spec');
        $this->systemContext->postOffer($user->id(), $specialization);

        $this->assertTrue($this->systemContext->system()->query(OfferThrottleQuery::class)->isThrottled($user->id()));
        $this->assertEquals(1, $this->systemContext->system()->query(OfferQuery::class)->total());
    }
}
