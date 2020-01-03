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

namespace HireInSocial\Tests\Offers\Application\Integration\Command\User;

use HireInSocial\Offers\Application\Command\User\FacebookConnect;
use HireInSocial\Offers\Application\Exception\Exception;
use HireInSocial\Tests\Offers\Application\Integration\HireInSocialTestCase;
use Ramsey\Uuid\Uuid;

final class FacebookConnectHandlerTest extends HireInSocialTestCase
{
    public function test_adding_extra_offers() : void
    {
        $this->systemContext->offersFacade()->handle(
            new FacebookConnect(
                Uuid::uuid4()->toString(),
                'user@hirein.social'
            )
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Email user@hirein.social already used by different account');

        $this->systemContext->offersFacade()->handle(
            new FacebookConnect(
                Uuid::uuid4()->toString(),
                'user@hirein.social'
            )
        );
    }
}
