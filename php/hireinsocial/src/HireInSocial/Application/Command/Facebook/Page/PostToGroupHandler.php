<?php

declare(strict_types=1);

namespace HireInSocial\Application\Command\Facebook\Page;

use HireInSocial\Application\Facebook\FacebookGroupService;
use HireInSocial\Application\Facebook\Draft;
use HireInSocial\Application\Facebook\Post;
use HireInSocial\Application\Facebook\Posts;
use HireInSocial\Application\Offer\Contact;
use HireInSocial\Application\Offer\Contract;
use HireInSocial\Application\Offer\Offer;
use HireInSocial\Application\Offer\Company;
use HireInSocial\Application\Offer\Description;
use HireInSocial\Application\Offer\Location;
use HireInSocial\Application\Offer\OfferFormatter;
use HireInSocial\Application\Offer\Offers;
use HireInSocial\Application\Offer\Position;
use HireInSocial\Application\Offer\Salary;
use HireInSocial\Application\Specialization\Specializations;
use HireInSocial\Application\System\Calendar;
use HireInSocial\Application\System\Handler;

final class PostToGroupHandler implements Handler
{
    private $calendar;
    private $offers;
    private $facebookGroupService;
    private $formatter;
    private $posts;
    private $specializations;

    public function __construct(
        Calendar $calendar,
        Offers $offers,
        Posts $posts,
        FacebookGroupService $facebookGroupService,
        OfferFormatter $formatter,
        Specializations $specializations
    ) {
        $this->calendar = $calendar;
        $this->facebookGroupService = $facebookGroupService;
        $this->formatter = $formatter;
        $this->offers = $offers;
        $this->posts = $posts;
        $this->specializations = $specializations;
    }

    public function handles(): string
    {
        return PostToGroup::class;
    }

    public function __invoke(PostToGroup $command) : void
    {
        $specialization = $this->specializations->get($command->specialization());

        $offer = $this->createOffer($command);

        $draft = new Draft(
            $command->fbUserId(),
            $this->formatter->format(
                $offer
            ),
            $command->offer()->company()->url()
        );
        $postId = $this->facebookGroupService->postAtGroupAs(
            $draft,
            $specialization->facebookChannel()->group(),
            $specialization->facebookChannel()->page()
        );
        $this->posts->add(new Post($postId, $offer, $draft));
        $this->offers->add($offer);
    }

    private function createOffer(PostToGroup $command): Offer
    {
        return new Offer(
            $this->calendar,
            new Company(
                $command->offer()->company()->name(),
                $command->offer()->company()->url(),
                $command->offer()->company()->description()
            ),
            new Position(
                $command->offer()->position()->name(),
                $command->offer()->position()->description()
            ),
            new Location($command->offer()->location()->remote(), $command->offer()->location()->name()),
            new Salary(
                $command->offer()->salary()->min(),
                $command->offer()->salary()->max(),
                $command->offer()->salary()->currencyCode(),
                $command->offer()->salary()->isNet()
            ),
            new Contract(
                $command->offer()->contract()->type()
            ),
            new Description(
                $command->offer()->description()->requirements(),
                $command->offer()->description()->benefits()
            ),
            new Contact(
                $command->offer()->contact()->email(),
                $command->offer()->contact()->name(),
                $command->offer()->contact()->phone()
            )
        );
    }
}
