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

namespace HireInSocial\Offers\Infrastructure\Doctrine\DBAL\Application\Offer;

use Doctrine\DBAL\Connection;
use HireInSocial\Offers\Application\Hash\Encoder;
use HireInSocial\Offers\Application\Offer\Application\EmailHash;
use HireInSocial\Offers\Application\Query\Offer\ApplicationQuery;

final class DbalApplicationQuery implements ApplicationQuery
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Encoder
     */
    private $encoder;

    public function __construct(Connection $connection, Encoder $encoder)
    {
        $this->connection = $connection;
        $this->encoder = $encoder;
    }

    public function alreadyApplied(string $offerId, string $email) : bool
    {
        return (bool) $this->connection->fetchColumn(
            'SELECT COUNT(*) FROM his_job_offer_application WHERE offer_id = :offerId AND email_hash = :emailHash',
            [
            'offerId' => $offerId,
            'emailHash' => EmailHash::fromRaw($email, $this->encoder)->toString(),
        ]
        );
    }

    public function countFor(string $offerId) : int
    {
        return (int) $this->connection->fetchColumn('SELECT COUNT(*) FROM his_job_offer_application WHERE offer_id = :offerId', [
            'offerId' => $offerId,
        ]);
    }
}
