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
use ITOffers\Offers\Application\Specialization\Specializations;
use ITOffers\Offers\Application\Specialization\TwitterChannel;

final class SetTwitterChannelHandler implements Handler
{
    private Specializations $specializations;

    public function __construct(Specializations $specializations)
    {
        $this->specializations = $specializations;
    }

    public function handles() : string
    {
        return SetTwitterChannel::class;
    }

    public function __invoke(SetTwitterChannel $command) : void
    {
        $specialization = $this->specializations->get($command->specSlug());

        $specialization->setTwitter(new TwitterChannel(
            $command->accountId(),
            $command->screenName(),
            $command->oauthToken(),
            $command->oauthSecret()
        ));
    }
}
