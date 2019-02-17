<?php

declare(strict_types=1);

namespace HireInSocial\Application\Facebook;

use HireInSocial\Application\Exception\Exception;
use HireInSocial\Application\Offer\Throttle;
use HireInSocial\Application\Specialization\Specialization;

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
    public function pagePostAtGroup(Draft $draft, Specialization $specialization) : string
    {
        if ($this->throttle->isThrottled((string) $draft->userId())) {
            throw new Exception(sprintf('User "%s" throttled, can\'t post job offer.', $draft->userId()));
        }

        $postId = $this->facebook->postToGroupAsPage($draft, $specialization->facebookChannel()->group(), $specialization->facebookChannel()->page());
        $this->throttle->throttle((string) $draft->userId());

        return $postId;
    }
}
