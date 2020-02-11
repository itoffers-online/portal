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

namespace ITOffers\Offers\Infrastructure\Doctrine\DBAL\Application\Facebook;

use Doctrine\DBAL\Connection;
use ITOffers\Offers\Application\Query\Facebook\FacebookQuery;
use ITOffers\Offers\Application\Query\Facebook\Model\FacebookPost;
use Ramsey\Uuid\Uuid;

final class DbalFacebookFacebookQuery implements FacebookQuery
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findFacebookPost(string $offerId) : ?FacebookPost
    {
        $postData = $this->connection->fetchAssoc('
            SELECT fb_id, job_offer_id FROM itof_facebook_post WHERE job_offer_id = :offerId', [
            'offerId' => $offerId,
        ]);

        if (!$postData) {
            return null;
        }

        return new FacebookPost($postData['fb_id'], Uuid::fromString($postData['job_offer_id']));
    }
}
