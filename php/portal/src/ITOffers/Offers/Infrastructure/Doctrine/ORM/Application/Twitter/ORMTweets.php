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

namespace ITOffers\Offers\Infrastructure\Doctrine\ORM\Application\Twitter;

use Doctrine\ORM\EntityManager;
use ITOffers\Offers\Application\Offer\Offer;
use ITOffers\Offers\Application\Twitter\Tweet;
use ITOffers\Offers\Application\Twitter\Tweets;

final class ORMTweets implements Tweets
{
    private EntityManager $entityManager;

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
