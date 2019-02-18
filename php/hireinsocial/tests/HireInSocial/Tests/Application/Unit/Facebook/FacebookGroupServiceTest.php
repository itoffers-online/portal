<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Unit\Facebook;

use HireInSocial\Application\Exception\Exception;
use HireInSocial\Application\Facebook\Facebook;
use HireInSocial\Application\Facebook\FacebookGroupService;
use HireInSocial\Application\Offer\Throttle;
use HireInSocial\Tests\Application\MotherObject\Facebook\DraftMother;
use HireInSocial\Tests\Application\MotherObject\Specialization\SpecializationMother;
use PHPUnit\Framework\TestCase;

final class FacebookGroupServiceTest extends TestCase
{
    public function test_posting_as_a_throttled_user()
    {
        $facebook = $this->createFacebookMock();

        $throttle = $this->createThrottleMock();

        $throttle->expects($this->at(0))
            ->method('isThrottled')
            ->willReturn(true);

        $service = new FacebookGroupService($facebook, $throttle);
        $draft = DraftMother::random();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf('User "%s" throttled, can\'t post job offer.', $draft->userId()));

        $service->pagePostAtGroup($draft, SpecializationMother::random());
    }

    public function test_postig_offer_on_facebook_group_as_a_page()
    {
        $facebook = $this->createFacebookMock();

        $throttle = $this->createThrottleMock();

        $throttle->expects($this->at(0))
            ->method('isThrottled')
            ->willReturn(false);

        $service = new FacebookGroupService($facebook, $throttle);

        $facebook->expects($this->at(0))
            ->method('postToGroupAsPage');

        $throttle->expects($this->at(1))
            ->method('throttle');

        $service->pagePostAtGroup(DraftMother::random(), SpecializationMother::random());
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function createThrottleMock(): Throttle
    {
        return $this->getMockBuilder(Throttle::class)
            ->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function createFacebookMock(): Facebook
    {
        return $this->getMockBuilder(Facebook::class)
            ->getMock();
    }
}
