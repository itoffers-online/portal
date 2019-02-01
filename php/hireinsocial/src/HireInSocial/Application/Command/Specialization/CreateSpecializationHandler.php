<?php

declare (strict_types=1);

namespace HireInSocial\Application\Command\Specialization;

use HireInSocial\Application\Facebook\Group;
use HireInSocial\Application\Facebook\Page;
use HireInSocial\Application\Specialization\FacebookChannel;
use HireInSocial\Application\Specialization\Specialization;
use HireInSocial\Application\Specialization\Specializations;
use HireInSocial\Application\System\Handler;

final class CreateSpecializationHandler implements Handler
{
    /**
     * @var Specializations
     */
    private $specializations;

    public function __construct(Specializations $specializations)
    {
        $this->specializations = $specializations;
    }

    public function handles(): string
    {
        return CreateSpecialization::class;
    }

    public function __invoke(CreateSpecialization $command) : void
    {
        $this->specializations->add(new Specialization(
            $command->slug(),
            $command->name(),
            new FacebookChannel(
                new Page(
                    $command->facebookPageId(),
                    $command->facebookPageToken()
                ),
                new Group(
                    $command->facebookGroupId()
                )
            )
        ));
    }
}