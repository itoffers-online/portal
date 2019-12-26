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

namespace HireInSocial\Application\Command\Specialization;

use HireInSocial\Application\Facebook\Group;
use HireInSocial\Application\Facebook\Page;
use HireInSocial\Application\Specialization\FacebookChannel;
use HireInSocial\Application\Specialization\Specializations;
use HireInSocial\Application\System\Handler;

class SetFacebookChannelHandler implements Handler
{
    private $specializations;

    public function __construct(Specializations $specializations)
    {
        $this->specializations = $specializations;
    }

    public function handles() : string
    {
        return SetFacebookChannel::class;
    }

    public function __invoke(SetFacebookChannel $command) : void
    {
        $specialization = $this->specializations->get($command->specSlug());

        $specialization->setFacebook(new FacebookChannel(
            new Page($command->facebookPageId(), $command->facebookPageToken()),
            new Group($command->facebookGroupId())
        ));
    }
}
