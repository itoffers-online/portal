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

namespace HireInSocial\Offers\Application\Command\Offer;

use HireInSocial\Offers\Application\Exception\Exception;
use HireInSocial\Offers\Application\Facebook\Draft;
use HireInSocial\Offers\Application\Facebook\FacebookGroupService;
use HireInSocial\Offers\Application\Facebook\Post;
use HireInSocial\Offers\Application\Facebook\Posts;
use HireInSocial\Offers\Application\Offer\Company;
use HireInSocial\Offers\Application\Offer\Contact;
use HireInSocial\Offers\Application\Offer\Contract;
use HireInSocial\Offers\Application\Offer\Description;
use HireInSocial\Offers\Application\Offer\Location;
use HireInSocial\Offers\Application\Offer\Offer;
use HireInSocial\Offers\Application\Offer\OfferFormatter;
use HireInSocial\Offers\Application\Offer\OfferPDF;
use HireInSocial\Offers\Application\Offer\OfferPDFs;
use HireInSocial\Offers\Application\Offer\Offers;
use HireInSocial\Offers\Application\Offer\Position;
use HireInSocial\Offers\Application\Offer\Salary;
use HireInSocial\Offers\Application\Offer\Slug;
use HireInSocial\Offers\Application\Offer\Slugs;
use HireInSocial\Offers\Application\Offer\Throttling;
use HireInSocial\Offers\Application\Specialization\Specialization;
use HireInSocial\Offers\Application\Specialization\Specializations;
use HireInSocial\Offers\Application\System\Calendar;
use HireInSocial\Offers\Application\System\FileStorage;
use HireInSocial\Offers\Application\System\FileStorage\File;
use HireInSocial\Offers\Application\System\Handler;
use HireInSocial\Offers\Application\User\User;
use HireInSocial\Offers\Application\User\Users;
use Ramsey\Uuid\Uuid;

final class PostOfferHandler implements Handler
{
    /**
     * @var \HireInSocial\Offers\Application\System\Calendar
     */
    private $calendar;

    /**
     * @var \HireInSocial\Offers\Application\Offer\Offers
     */
    private $offers;

    /**
     * @var \HireInSocial\Offers\Application\User\Users
     */
    private $users;

    /**
     * @var \HireInSocial\Offers\Application\Facebook\Posts
     */
    private $posts;

    /**
     * @var \HireInSocial\Offers\Application\Offer\Throttling
     */
    private $throttling;

    /**
     * @var \HireInSocial\Offers\Application\Facebook\FacebookGroupService
     */
    private $facebookGroupService;

    /**
     * @var \HireInSocial\Offers\Application\Offer\OfferFormatter
     */
    private $formatter;

    /**
     * @var \HireInSocial\Offers\Application\Specialization\Specializations
     */
    private $specializations;

    /**
     * @var \HireInSocial\Offers\Application\Offer\Slugs
     */
    private $slugs;

    /**
     * @var \HireInSocial\Offers\Application\Offer\OfferPDFs
     */
    private $offerPDFs;

    /**
     * @var \HireInSocial\Offers\Application\System\FileStorage
     */
    private $fileStorage;

    public function __construct(
        Calendar $calendar,
        Offers $offers,
        Users $users,
        Posts $posts,
        Throttling $throttling,
        FacebookGroupService $facebookGroupService,
        OfferFormatter $formatter,
        Specializations $specializations,
        Slugs $slugs,
        OfferPDFs $offerPDFs,
        FileStorage $fileStorage
    ) {
        $this->calendar = $calendar;
        $this->offers = $offers;
        $this->users = $users;
        $this->posts = $posts;
        $this->throttling = $throttling;
        $this->facebookGroupService = $facebookGroupService;
        $this->formatter = $formatter;
        $this->specializations = $specializations;
        $this->slugs = $slugs;
        $this->offerPDFs = $offerPDFs;
        $this->fileStorage = $fileStorage;
    }

    public function handles() : string
    {
        return PostOffer::class;
    }

    public function __invoke(PostOffer $command) : void
    {
        $user = $this->users->getById(Uuid::fromString($command->userId()));

        $specialization = $this->specializations->get($command->specialization());

        $offer = $this->createOffer($command, $user, $specialization);

        if ($this->throttling->isThrottled($user, $this->offers)) {
            throw new Exception(sprintf('User "%s" is throttled', $user->id()->toString()));
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

        if ($command->offerPDFPath()) {
            $offerPDF = OfferPDF::forOffer($offer, $this->calendar);
            $this->fileStorage->upload(File::pdf($offerPDF->path(), $command->offerPDFPath()));
            $this->offerPDFs->add($offerPDF);
        }

        $this->offers->add($offer);
        $this->slugs->add(Slug::from($offer, $this->calendar));
    }

    private function createOffer(PostOffer $command, User $user, Specialization $specialization) : Offer
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
            $command->offer()->location()->name()
                ? Location::atPlace(
                    $command->offer()->location()->remote(),
                    $command->offer()->location()->name(),
                    $command->offer()->location()->latLng()->lat(),
                    $command->offer()->location()->latLng()->lng(),
                )
                : Location::onlyRemote(),
            $command->offer()->salary()
                ? new Salary(
                    $command->offer()->salary()->min(),
                    $command->offer()->salary()->max(),
                    $command->offer()->salary()->currencyCode(),
                    $command->offer()->salary()->isNet(),
                    Salary\Period::fromString(\mb_strtoupper($command->offer()->salary()->periodType()))
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
