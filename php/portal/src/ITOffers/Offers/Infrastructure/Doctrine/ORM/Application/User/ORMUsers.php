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

namespace ITOffers\Offers\Infrastructure\Doctrine\ORM\Application\User;

use Doctrine\ORM\EntityManager;
use ITOffers\Offers\Application\Exception\Exception;
use ITOffers\Offers\Application\User\User;
use ITOffers\Offers\Application\User\Users;
use Ramsey\Uuid\UuidInterface;

final class ORMUsers implements Users
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(User $user) : void
    {
        $this->entityManager->persist($user);
    }

    public function getById(UuidInterface $id) : User
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $id->toString()]);

        if (!$user) {
            throw new Exception(sprintf('User with id %s does not exists.', (string) $id->toString()));
        }

        return $user;
    }

    public function getByFB(string $userAppId) : User
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['fbUserAppId' => $userAppId]);

        if (!$user) {
            throw new Exception(sprintf('User with user facebook app id %s does not exists.', $userAppId));
        }

        return $user;
    }

    public function getByLinkedIn(string $userAppId) : User
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['linkedInUserAppId' => $userAppId]);

        if (!$user) {
            throw new Exception(sprintf('User with user facebook app id %s does not exists.', $userAppId));
        }

        return $user;
    }

    public function emailExists(string $email) : bool
    {
        return (bool) $this->entityManager->getRepository(User::class)->findOneBy(['email' => \mb_strtolower($email)]);
    }
}
