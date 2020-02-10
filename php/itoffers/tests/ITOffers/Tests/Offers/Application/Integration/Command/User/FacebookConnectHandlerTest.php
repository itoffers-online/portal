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

namespace ITOffers\Tests\Offers\Application\Integration\Command\User;

use ITOffers\Offers\Application\Command\User\FacebookConnect;
use ITOffers\Offers\Application\Exception\Exception;
use ITOffers\Tests\Offers\Application\Integration\OffersTestCase;
use Ramsey\Uuid\Uuid;

final class FacebookConnectHandlerTest extends OffersTestCase
{
    public function test_adding_extra_offers() : void
    {
        $this->systemContext->offersFacade()->handle(
            new FacebookConnect(
                Uuid::uuid4()->toString(),
                'user@itoffers.online'
            )
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Email user@itoffers.online already used by different account');

        $this->systemContext->offersFacade()->handle(
            new FacebookConnect(
                Uuid::uuid4()->toString(),
                'user@itoffers.online'
            )
        );
    }
}