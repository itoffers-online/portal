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

namespace HireInSocial\Tests\Application\Integration\Command\Throttle;

use HireInSocial\Application\Query\Offer\OfferThrottleQuery;
use HireInSocial\Tests\Application\Integration\HireInSocialTestCase;
use HireInSocial\Tests\Application\MotherObject\Command\Offer\PostOfferMother;

final class RemoteThrottleTest extends HireInSocialTestCase
{
    public function test_removing_throttle_from_job_offer_author()
    {
        $user = $this->systemContext->createUser();

        $this->systemContext->createSpecialization($specialization = 'spec');
        $this->systemContext->system()->handle(PostOfferMother::random($user->id(), $specialization));

        $this->assertTrue($this->systemContext->system()->query(OfferThrottleQuery::class)->isThrottled($user->id()));

        $this->systemContext->removeThrottle($user->id());

        $this->assertFalse($this->systemContext->system()->query(OfferThrottleQuery::class)->isThrottled($user->id()));
    }
}
