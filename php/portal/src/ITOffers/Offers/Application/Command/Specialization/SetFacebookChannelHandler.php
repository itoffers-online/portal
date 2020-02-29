<?php

declare(strict_types=1);

/*
 * This file is part of the itoffers.online project.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ITOffers\Offers\Application\Command\Specialization;

use ITOffers\Component\CQRS\System\Handler;
use ITOffers\Offers\Application\Facebook\Group;
use ITOffers\Offers\Application\Facebook\Page;
use ITOffers\Offers\Application\Specialization\FacebookChannel;
use ITOffers\Offers\Application\Specialization\Specializations;

final class SetFacebookChannelHandler implements Handler
{
    private Specializations $specializations;

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
            new Page(
                $command->facebookPageId(),
                $command->facebookPageToken()
            ),
            new Group(
                $command->facebookGroupId(),
                $command->facebookGroupName()
            )
        ));
    }
}
