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

namespace ITOffers\Offers\Application\Command\Twitter;

use ITOffers\Component\CQRS\System\Handler;
use ITOffers\Offers\Application\Exception\Exception;
use ITOffers\Offers\Application\Offer\Offers;
use ITOffers\Offers\Application\Specialization\Specializations;
use ITOffers\Offers\Application\Twitter\Tweet;
use ITOffers\Offers\Application\Twitter\Tweets;
use ITOffers\Offers\Application\Twitter\Twitter;
use Ramsey\Uuid\Uuid;

final class TweetAboutOfferHandler implements Handler
{
    /**
     * @var Offers
     */
    private $offers;

    /**
     * @var Tweets
     */
    private $tweets;

    /**
     * @var Specializations
     */
    private $specializations;

    /**
     * @var Twitter
     */
    private $twitter;

    public function __construct(
        Offers $offers,
        Tweets $tweets,
        Specializations $specializations,
        Twitter $twitter
    ) {
        $this->offers = $offers;
        $this->tweets = $tweets;
        $this->specializations = $specializations;
        $this->twitter = $twitter;
    }

    public function handles() : string
    {
        return TweetAboutOffer::class;
    }

    public function __invoke(TweetAboutOffer $command) : void
    {
        $offer = $this->offers->getById(Uuid::fromString($command->offerId()));
        $post = $this->tweets->findFor($offer);

        if ($post !== null) {
            throw new Exception(\sprintf("Offer \"%s\" was already posted at Facebook", $offer->id()->toString()));
        }

        $this->tweets->add(
            new Tweet(
                $this->twitter->tweet(
                    $command->message(),
                    $this->specializations->getFor($offer)->twitterChannel()
                ),
                $offer
            )
        );
    }
}
