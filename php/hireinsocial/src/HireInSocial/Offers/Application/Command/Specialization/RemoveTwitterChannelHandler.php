<?php

declare(strict_types=1);

/*
 * This file is part of the Hire in Social project.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HireInSocial\Offers\Application\Command\Specialization;

use HireInSocial\Component\CQRS\System\Handler;
use HireInSocial\Offers\Application\Specialization\Specializations;

final class RemoveTwitterChannelHandler implements Handler
{
    /**
     * @var Specializations
     */
    private $specializations;

    public function __construct(Specializations $specializations)
    {
        $this->specializations = $specializations;
    }

    public function handles() : string
    {
        return RemoveTwitterChannel::class;
    }

    public function __invoke(RemoveTwitterChannel $command) : void
    {
        $specialization = $this->specializations->get($command->specSlug());

        $specialization->removeTwitter();
    }
}
