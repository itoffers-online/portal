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

namespace HireInSocial\Offers\Application\Command\Facebook;

use HireInSocial\Component\CQRS\System\Handler;
use HireInSocial\Offers\Application\Exception\Exception;
use HireInSocial\Offers\Application\Facebook\Draft;
use HireInSocial\Offers\Application\Facebook\FacebookGroupService;
use HireInSocial\Offers\Application\Facebook\Post;
use HireInSocial\Offers\Application\Facebook\Posts;
use HireInSocial\Offers\Application\Offer\Offers;
use HireInSocial\Offers\Application\Specialization\Specializations;
use Ramsey\Uuid\Uuid;

final class PagePostOfferAtGroupHandler implements Handler
{
    /**
     * @var Offers
     */
    private $offers;

    /**
     * @var Posts
     */
    private $posts;

    /**
     * @var Specializations
     */
    private $specializations;

    /**
     * @var FacebookGroupService
     */
    private $facebookGroupService;

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
