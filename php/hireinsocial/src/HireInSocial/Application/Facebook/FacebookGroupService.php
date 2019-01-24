<?php

declare (strict_types=1);

namespace HireInSocial\Application\Facebook;

use HireInSocial\Application\Exception\Exception;
use HireInSocial\Application\Offer\Throttle;

final class FacebookGroupService
{
    private $facebook;
    private $throttle;

    public function __construct(Facebook $facebook, Throttle $throttle)
    {
        $this->facebook = $facebook;
        $this->throttle = $throttle;
    }

    /**
     * @throws Exception
     */
    public function postAtGroupAs(Draft $draft, Group $group, Page $page) : string
    {
        if (!$this->facebook->userExists($draft->authorFbId())) {
            throw new Exception(sprintf('"%s" is not valid Facebook author id', $draft->authorFbId()));
        }

        if ($this->throttle->isThrottled($draft->authorFbId())) {
            throw new Exception(sprintf('User "%s" throttled, can\'t post job offer.', $draft->authorFbId()));
        }

        $postId = $this->facebook->postToGroupAsPage($draft, $group, $page);
        $this->throttle->throttle($draft->authorFbId());

        return $postId;
    }
}