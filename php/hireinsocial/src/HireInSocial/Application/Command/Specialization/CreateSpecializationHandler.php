<?php

declare(strict_types=1);

namespace HireInSocial\Application\Command\Specialization;

use HireInSocial\Application\Specialization\Specialization;
use HireInSocial\Application\Specialization\Specializations;
use HireInSocial\Application\System\Handler;

final class CreateSpecializationHandler implements Handler
{
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
        $this->specializations->add(new Specialization($command->slug()));
    }
}
