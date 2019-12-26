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

namespace HireInSocial\Infrastructure\Doctrine\ORM\Application\User;

use Doctrine\ORM\EntityManager;
use HireInSocial\Application\Exception\Exception;
use HireInSocial\Application\User\User;
use HireInSocial\Application\User\Users;
use Ramsey\Uuid\UuidInterface;

final class ORMUsers implements Users
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(User $user): void
    {
        $this->entityManager->persist($user);
    }

    public function getById(UuidInterface $id): User
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $id->toString()]);

        if (!$user) {
            throw new Exception(sprintf('User with id %s does not exists.', (string) $id->toString()));
        }

        return $user;
    }

    public function getByFB(string $userAppId): User
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['fbUserAppId' => $userAppId]);

        if (!$user) {
            throw new Exception(sprintf('User with user facebook app id %s does not exists.', $userAppId));
        }

        return $user;
    }
}
