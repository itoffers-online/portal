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

use HireInSocial\Offers\Application\Specialization\Specializations;
use HireInSocial\Offers\Application\Specialization\TwitterChannel;
use HireInSocial\Offers\Application\System\Handler;

final class SetTwitterChannelHandler implements Handler
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
