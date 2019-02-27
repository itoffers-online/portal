<?php

declare(strict_types=1);

namespace HireInSocial\Application\Facebook;

use HireInSocial\Application\Exception\Exception;
use HireInSocial\Application\Specialization\Specialization;

final class FacebookGroupService
{
    private $facebook;

    public function __construct(Facebook $facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * @throws Exception
     */
    public function pagePostAtGroup(Draft $draft, Specialization $specialization) : string
    {
        if (!$specialization->facebookChannel()) {
            throw new Exception(sprintf('Specialization "%s" does not have facebook channel assigned.', $specialization->slug()));
        }

        return $this->facebook->postToGroupAsPage(
            $draft,
            $specialization->facebookChannel()->group(),
            $specialization->facebookChannel()->page()
        );
    }
}
