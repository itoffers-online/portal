<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Unit\Facebook;

use HireInSocial\Application\Exception\Exception;
use HireInSocial\Application\Facebook\Facebook;
use HireInSocial\Application\Facebook\FacebookGroupService;
use HireInSocial\Application\Offer\Throttle;
use HireInSocial\Tests\Application\MotherObject\Facebook\GroupMother;
use HireInSocial\Tests\Application\MotherObject\Facebook\PageMother;
use HireInSocial\Tests\Application\MotherObject\Facebook\PostMother;
use PHPUnit\Framework\TestCase;

final class FacebookGroupServiceTest extends TestCase
{
    public function test_posting_as_a_user_with_invalid_id()
    {
        $authorId = 'invalid-id';

        $facebook = $this->createFacebookMock();

        $facebook->expects($this->any())
            ->method('userExists')
            ->with($authorId)
            ->willReturn(false);

        $service = new FacebookGroupService($facebook, $this->createThrottleMock());

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('"invalid-id" is not valid Facebook author id');

        $service->postAtGroupAs(PostMother::withAuthor($authorId), GroupMother::random(), PageMother::random());
    }

    public function test_posting_as_a_throttled_user()
    {
        $facebook = $this->createFacebookMock();

        $facebook->expects($this->at(0))
            ->method('userExists')
            ->willReturn(true);

        $throttle = $this->createThrottleMock();

        $throttle->expects($this->at(0))
            ->method('isThrottled')
            ->willReturn(true);

        $service = new FacebookGroupService($facebook, $throttle);
        $post = PostMother::random();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf('User "%s" throttled, can\'t post job offer.', $post->authorFbId()));

        $service->postAtGroupAs($post, GroupMother::random(), PageMother::random());
    }

    public function test_postig_offer_on_facebook_group_as_a_page()
    {
        $facebook = $this->createFacebookMock();

        $facebook->expects($this->at(0))
            ->method('userExists')
            ->willReturn(true);

        $throttle = $this->createThrottleMock();

        $throttle->expects($this->at(0))
            ->method('isThrottled')
            ->willReturn(false);

        $service = new FacebookGroupService($facebook, $throttle);

        $facebook->expects($this->at(1))
            ->method('postToGroupAsPage');

        $throttle->expects($this->at(1))
            ->method('throttle');

        $service->postAtGroupAs(PostMother::random(), GroupMother::random(), PageMother::random());
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
