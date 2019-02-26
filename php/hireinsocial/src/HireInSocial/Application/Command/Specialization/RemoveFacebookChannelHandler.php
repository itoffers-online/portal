<?php

declare(strict_types=1);

namespace HireInSocial\Application\Command\Specialization;

use HireInSocial\Application\Specialization\Specializations;
use HireInSocial\Application\System\Handler;

class RemoveFacebookChannelHandler implements Handler
{
    private $specializations;

    public function __construct(Specializations $specializations)
    {
        $this->specializations = $specializations;
    }

    public function handles(): string
    {
        return RemoveFacebookChannel::class;
    }

    public function __invoke(RemoveFacebookChannel $command) : void
    {
        $specialization = $this->specializations->get($command->specSlug());

        $specialization->removeFacebook();
    }
}
