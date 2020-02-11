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

namespace ITOffers\Offers\Infrastructure\Doctrine\DBAL\Application\Twitter;

use Doctrine\DBAL\Connection;
use ITOffers\Offers\Application\Query\Twitter\Model\Tweet;
use ITOffers\Offers\Application\Query\Twitter\TweetsQuery;

final class DbalTweetsQuery implements TweetsQuery
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findTweet(string $offerId) : ?Tweet
    {
        $tweetsData = $this->connection->createQueryBuilder()
            ->select('id, job_offer_id')
            ->from('itof_twitter_tweet', 'tw')
            ->where('tw.job_offer_id = :jobOfferId')
            ->setMaxResults(1)
            ->setParameters(
                [
                    'jobOfferId' => $offerId,
                ]
            )->execute()
            ->fetchAll();

        $postData = $this->connection->fetchAssoc('
            SELECT fb_id, job_offer_id FROM itof_facebook_post WHERE job_offer_id = :offerId', [
            'offerId' => $offerId,
        ]);

        if (!\count($tweetsData)) {
            return null;
        }

        $tweetData = \current($tweetsData);

        return new Tweet($tweetData['id']);
    }
}
