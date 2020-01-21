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

namespace HireInSocial\Offers\Application\Command\Twitter;

use HireInSocial\Offers\Application\Exception\Exception;
use HireInSocial\Offers\Application\Offer\Offers;
use HireInSocial\Offers\Application\Specialization\Specializations;
use HireInSocial\Offers\Application\System\Handler;
use HireInSocial\Offers\Application\Twitter\Tweet;
use HireInSocial\Offers\Application\Twitter\Tweets;
use HireInSocial\Offers\Application\Twitter\Twitter;
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
