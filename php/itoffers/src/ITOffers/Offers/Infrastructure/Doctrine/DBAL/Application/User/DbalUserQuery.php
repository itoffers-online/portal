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

namespace ITOffers\Offers\Infrastructure\Doctrine\DBAL\Application\User;

use Doctrine\DBAL\Connection;
use ITOffers\Offers\Application\Query\User\Model\User;
use ITOffers\Offers\Application\Query\User\UserQuery;

final class DbalUserQuery implements UserQuery
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findByFacebook(string $facebookUserAppId) : ?User
    {
        $userData = $this->connection->createQueryBuilder()
            ->select('u.*')
            ->from('his_user', 'u')
            ->where('u.fb_user_app_id = :facebookUserAppId')
            ->setParameters(
                [
                    'facebookUserAppId' => $facebookUserAppId,
                ]
            )->execute()
            ->fetch();

        if (!$userData) {
            return null;
        }

        return $this->hydrateUser($userData);
    }

    public function findByLinkedIn(string $linkedInUserAppId) : ?User
    {
        $userData = $this->connection->createQueryBuilder()
            ->select('u.*')
            ->from('his_user', 'u')
            ->where('u.linked_in_user_app_id = :linkedInUserAppId')
            ->setParameters(
                [
                    'linkedInUserAppId' => $linkedInUserAppId,
                ]
            )->execute()
            ->fetch();

        if (!$userData) {
            return null;
        }

        return $this->hydrateUser($userData);
    }

    public function findById(string $id) : ?User
    {
        $userData = $this->connection->createQueryBuilder()
            ->select('u.*')
            ->from('his_user', 'u')
            ->where('u.id = :id')
            ->setParameters(
                [
                    'id' => $id,
                ]
            )->execute()
            ->fetch();

        if (!$userData) {
            return null;
        }

        return $this->hydrateUser($userData);
    }

    public function findByEmail(string $email) : ?User
    {
        $userData = $this->connection->createQueryBuilder()
            ->select('u.*')
            ->from('his_user', 'u')
            ->where('u.email_address = :email')
            ->setParameters(
                [
                    'email' => \mb_strtolower($email),
                ]
            )->execute()
            ->fetch();

        if (!$userData) {
            return null;
        }

        return $this->hydrateUser($userData);
    }

    private function hydrateUser(array $userData) : User
    {
        return new User(
            $userData['id'],
            $userData['email_address'],
            $userData['fb_user_app_id'],
            $userData['linked_in_user_app_id'],
            (bool) $userData['blocked_at']
        );
    }
}
