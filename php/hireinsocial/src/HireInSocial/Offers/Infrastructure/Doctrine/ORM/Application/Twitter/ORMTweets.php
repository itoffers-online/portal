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

namespace HireInSocial\Offers\Infrastructure\Doctrine\ORM\Application\Twitter;

use Doctrine\ORM\EntityManager;
use HireInSocial\Offers\Application\Offer\Offer;
use HireInSocial\Offers\Application\Twitter\Tweet;
use HireInSocial\Offers\Application\Twitter\Tweets;

final class ORMTweets implements Tweets
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(Tweet $tweet) : void
    {
        $this->entityManager->persist($tweet);
    }

    public function findFor(Offer $offer) : ?Tweet
    {
        return $this->entityManager->getRepository(Tweet::class)->findOneBy(['jobOfferId' => $offer->id()->toString()]);
    }
}
