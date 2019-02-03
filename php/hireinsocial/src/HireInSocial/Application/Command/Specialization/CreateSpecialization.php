<?php

declare(strict_types=1);

namespace HireInSocial\Application\Command\Specialization;

use HireInSocial\Application\Command\ClassCommand;
use HireInSocial\Application\System\Command;

final class CreateSpecialization implements Command
{
    use ClassCommand;

    private $slug;
    private $facebookPageId;
    private $facebookPageToken;
    private $facebookGroupId;

    public function __construct(string $slug, string $facebookPageId, string $facebookPageToken, string $facebookGroupId)
    {
        $this->slug = $slug;
        $this->facebookPageId = $facebookPageId;
        $this->facebookPageToken = $facebookPageToken;
        $this->facebookGroupId = $facebookGroupId;
    }

    public function slug(): string
    {
        return $this->slug;
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
