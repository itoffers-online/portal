<?php

declare(strict_types=1);

namespace HireInSocial\Infrastructure\Doctrine\DBAL\Application\User;

use Doctrine\DBAL\Connection;
use HireInSocial\Application\Query\User\Model\User;
use HireInSocial\Application\Query\User\UserQuery;

final class DBALUserQuery implements UserQuery
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findByFacebook(string $facebookUserAppId): ?User
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

    private function hydrateUser(array $userData) : User
    {
        return new User(
            $userData['id'],
            $userData['fb_user_app_id']
        );
    }
}
