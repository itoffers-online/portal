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

namespace HireInSocial\Application\Command\Offer;

use HireInSocial\Application\Exception\Exception;
use HireInSocial\Application\Facebook\Draft;
use HireInSocial\Application\Facebook\FacebookGroupService;
use HireInSocial\Application\Facebook\Post;
use HireInSocial\Application\Facebook\Posts;
use HireInSocial\Application\Offer\Company;
use HireInSocial\Application\Offer\Contact;
use HireInSocial\Application\Offer\Contract;
use HireInSocial\Application\Offer\Description;
use HireInSocial\Application\Offer\Location;
use HireInSocial\Application\Offer\Offer;
use HireInSocial\Application\Offer\OfferFormatter;
use HireInSocial\Application\Offer\Offers;
use HireInSocial\Application\Offer\Position;
use HireInSocial\Application\Offer\Salary;
use HireInSocial\Application\Offer\Slug;
use HireInSocial\Application\Offer\Slugs;
use HireInSocial\Application\Offer\Throttle;
use HireInSocial\Application\Specialization\Specialization;
use HireInSocial\Application\Specialization\Specializations;
use HireInSocial\Application\System\Calendar;
use HireInSocial\Application\System\Handler;
use HireInSocial\Application\User\User;
use HireInSocial\Application\User\Users;
use Ramsey\Uuid\Uuid;

final class PostOfferHandler implements Handler
{
    private $calendar;
    private $offers;
    private $users;
    private $posts;
    private $facebookGroupService;
    private $formatter;
    private $specializations;
    private $throttle;
    private $slugs;

    public function __construct(
        Calendar $calendar,
        Offers $offers,
        Users $users,
        Posts $posts,
        FacebookGroupService $facebookGroupService,
        OfferFormatter $formatter,
        Specializations $specializations,
        Throttle $throttle,
        Slugs $slugs
    ) {
        $this->calendar = $calendar;
        $this->offers = $offers;
        $this->users = $users;
        $this->posts = $posts;
        $this->facebookGroupService = $facebookGroupService;
        $this->formatter = $formatter;
        $this->specializations = $specializations;
        $this->slugs = $slugs;
        $this->throttle = $throttle;
    }

    public function handles(): string
    {
        return PostOffer::class;
    }

    public function __invoke(PostOffer $command) : void
    {
        $user = $this->users->getById(Uuid::fromString($command->userId()));

        $specialization = $this->specializations->get($command->specialization());

        $offer = $this->createOffer($command, $user, $specialization);

        if ($this->throttle->isThrottled((string) $user->id())) {
            throw new Exception(sprintf('User "%s" is throttled', (string) $user->id()));
        }

        if ($command->offer()->channels()->facebookGroup()) {
            $draft = Draft::createFor(
                $user,
                $this->formatter,
                $offer
            );

            $this->posts->add(
                new Post(
                    $this->facebookGroupService->pagePostAtGroup(
                        $draft,
                        $specialization
                    ),
                    $offer
                )
            );
        }

        $this->offers->add($offer);
        $this->slugs->add(Slug::from($offer, $this->calendar));
        $this->throttle->throttle((string) $user->id());
    }

    private function createOffer(PostOffer $command, User $user, Specialization $specialization): Offer
    {
        return Offer::postIn(
            $specialization,
            $user,
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
            $command->offer()->salary()
                ? new Salary(
                    $command->offer()->salary()->min(),
                    $command->offer()->salary()->max(),
                    $command->offer()->salary()->currencyCode(),
                    $command->offer()->salary()->isNet()
                )
                : null,
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
            ),
            $this->calendar
        );
    }
}
