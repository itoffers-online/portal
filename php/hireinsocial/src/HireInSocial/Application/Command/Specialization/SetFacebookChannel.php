<?php

declare(strict_types=1);

namespace HireInSocial\Application\Command\Specialization;

use HireInSocial\Application\Command\ClassCommand;
use HireInSocial\Application\System\Command;

class SetFacebookChannel implements Command
{
    use ClassCommand;

    private $specSlug;
    private $facebookPageId;
    private $facebookPageToken;
    private $facebookGroupId;

    public function __construct(
        string $specSlug,
        string $facebookPageId,
        string $facebookPageToken,
        string $facebookGroupId
    ) {
        $this->specSlug = $specSlug;
        $this->facebookPageId = $facebookPageId;
        $this->facebookPageToken = $facebookPageToken;
        $this->facebookGroupId = $facebookGroupId;
    }

    public function specSlug(): string
    {
        return $this->specSlug;
    }

    public function facebookPageId(): string
    {
        return $this->facebookPageId;
    }

    public function facebookPageToken(): string
    {
        return $this->facebookPageToken;
    }

    public function facebookGroupId(): string
    {
        return $this->facebookGroupId;
    }
}
