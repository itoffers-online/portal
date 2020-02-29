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

namespace ITOffers\Offers\Infrastructure\Doctrine\DBAL\Application\Offer;

use Doctrine\DBAL\Connection;
use ITOffers\Offers\Application\Hash\Encoder;
use ITOffers\Offers\Application\Offer\Application\EmailHash;
use ITOffers\Offers\Application\Query\Offer\ApplicationQuery;

final class DbalApplicationQuery implements ApplicationQuery
{
    private Connection $connection;

    private Encoder $encoder;

    public function __construct(Connection $connection, Encoder $encoder)
    {
        $this->connection = $connection;
        $this->encoder = $encoder;
    }

    public function alreadyApplied(string $offerId, string $email) : bool
    {
        return (bool) $this->connection->fetchColumn(
            'SELECT COUNT(*) FROM itof_job_offer_application WHERE offer_id = :offerId AND email_hash = :emailHash',
            [
            'offerId' => $offerId,
            'emailHash' => EmailHash::fromRaw($email, $this->encoder)->toString(),
        ]
        );
    }

    public function countFor(string $offerId) : int
    {
        return (int) $this->connection->fetchColumn('SELECT COUNT(*) FROM itof_job_offer_application WHERE offer_id = :offerId', [
            'offerId' => $offerId,
        ]);
    }
}
