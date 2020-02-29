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

namespace ITOffers\Offers\Application\Command\Facebook;

use ITOffers\Component\CQRS\System\Handler;
use ITOffers\Offers\Application\Exception\Exception;
use ITOffers\Offers\Application\Facebook\Draft;
use ITOffers\Offers\Application\Facebook\FacebookGroupService;
use ITOffers\Offers\Application\Facebook\Post;
use ITOffers\Offers\Application\Facebook\Posts;
use ITOffers\Offers\Application\Offer\Offers;
use ITOffers\Offers\Application\Specialization\Specializations;
use Ramsey\Uuid\Uuid;

final class PagePostOfferAtGroupHandler implements Handler
{
    private Offers $offers;

    private Posts $posts;

    private Specializations $specializations;

    private FacebookGroupService $facebookGroupService;

    public function __construct(
        Offers $offers,
        Posts $posts,
        Specializations $specializations,
        FacebookGroupService $facebookGroupService
    ) {
        $this->offers = $offers;
        $this->posts = $posts;
        $this->specializations = $specializations;
        $this->facebookGroupService = $facebookGroupService;
    }

    public function handles() : string
    {
        return PagePostOfferAtGroup::class;
    }

    public function __invoke(PagePostOfferAtGroup $command) : void
    {
        $offer = $this->offers->getById(Uuid::fromString($command->offerId()));
        $post = $this->posts->findFor($offer);

        if ($post !== null) {
            throw new Exception(\sprintf("Offer \"%s\" was already posted at Facebook", $offer->id()->toString()));
        }

        $this->posts->add(
            new Post(
                $this->facebookGroupService->pagePostAtGroup(
                    Draft::createFor($offer, $command->message()),
                    $this->specializations->getFor($offer)
                ),
                $offer
            )
        );
    }
}
