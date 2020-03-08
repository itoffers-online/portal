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

namespace ITOffers\Notifications\Infrastructure\Users;

use ITOffers\Notifications\Application\User\User;
use ITOffers\Notifications\Application\Users;
use ITOffers\Offers\Offers as OffersModule;
use Ramsey\Uuid\UuidInterface;

final class ModuleOffersUsers implements Users
{
    private OffersModule $offersModule;

    public function __construct(OffersModule $offersModule)
    {
        $this->offersModule = $offersModule;
    }

    public function getById(UuidInterface $id) : User
    {
        return new User(
            $this->offersModule->userQuery()->findById($id->toString())->email()
        );
    }
}
