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

use HireInSocial\Offers\Application\Command\Offer\Offer\Description\Requirements;
use HireInSocial\Offers\Application\EventStream;
use HireInSocial\Offers\Application\Exception\Exception;
use HireInSocial\Offers\Application\Offer\Company;
use HireInSocial\Offers\Application\Offer\Contact;
use HireInSocial\Offers\Application\Offer\Contract;
use HireInSocial\Offers\Application\Offer\Description;
use HireInSocial\Offers\Application\Offer\Event\OfferPostedEvent;
use HireInSocial\Offers\Application\Offer\Locale;
use HireInSocial\Offers\Application\Offer\Location;
use HireInSocial\Offers\Application\Offer\Offer;
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
use HireInSocial\Offers\Application\User\ExtraOffers;
use HireInSocial\Offers\Application\User\User;
use HireInSocial\Offers\Application\User\Users;
use Ramsey\Uuid\Uuid;

final class PostOfferHandler implements Handler
{
    /**
     * @var Calendar
     */
    private $calendar;

    /**
     * @var Offers
     */
    private $offers;

    /**
     * @var Users
     */
    private $users;

    /**
     * @var Throttling
     */
    private $throttling;

    /**
     * @var Specializations
     */
    private $specializations;

    /**
     * @var Slugs
     */
    private $slugs;

    /**
     * @var OfferPDFs
     */
    private $offerPDFs;

    /**
     * @var FileStorage
     */
    private $fileStorage;

    /**
     * @var ExtraOffers
     */
    private $extraOffers;

    /**
     * @var EventStream
     */
    private $eventStream;

    public function __construct(
        Calendar $calendar,
        Offers $offers,
        ExtraOffers $extraOffers,
        Users $users,
        Throttling $throttling,
        Specializations $specializations,
        Slugs $slugs,
        OfferPDFs $offerPDFs,
        FileStorage $fileStorage,
        EventStream $eventStream
    ) {
        $this->calendar = $calendar;
        $this->offers = $offers;
        $this->users = $users;
        $this->throttling = $throttling;
        $this->specializations = $specializations;
        $this->slugs = $slugs;
        $this->offerPDFs = $offerPDFs;
        $this->fileStorage = $fileStorage;
        $this->extraOffers = $extraOffers;
        $this->eventStream = $eventStream;
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
        $slug = Slug::from($offer, $this->calendar);

        if ($this->throttling->isThrottled($user, $this->offers)) {
            $extraOffer = $this->extraOffers->findClosesToExpire($user->id());

            if ($extraOffer) {
                $extraOffer->useFor($offer, $this->calendar);
            } else {
                throw new Exception(sprintf('User "%s" is throttled', $user->id()->toString()));
            }
        }

        if ($command->offerPDFPath()) {
            $offerPDF = OfferPDF::forOffer($offer, $this->calendar);
            $this->fileStorage->upload(File::pdf($offerPDF->path(), $command->offerPDFPath()));
            $this->offerPDFs->add($offerPDF);
        }

        $this->offers->add($offer);
        $this->slugs->add($slug);

        $this->eventStream->record(new OfferPostedEvent($offer));
    }

    private function createOffer(PostOffer $command, User $user, Specialization $specialization) : Offer
    {
        $location = Location::remote();

        if ($command->offer()->location()->remote() && $command->offer()->location()->latLng()) {
            $location = Location::partiallyRemote(
                $command->offer()->location()->countryCode(),
                $command->offer()->location()->city(),
                $command->offer()->location()->latLng()->lat(),
                $command->offer()->location()->latLng()->lng(),
            );
        }

        if (!$command->offer()->location()->remote() && $command->offer()->location()->latLng()) {
            $location = Location::atOffice(
                $command->offer()->location()->countryCode(),
                $command->offer()->location()->city(),
                $command->offer()->location()->latLng()->lat(),
                $command->offer()->location()->latLng()->lng(),
            );
        }

        return Offer::post(
            Uuid::fromString($command->offerId()),
            $specialization,
            new Locale($command->locale()),
            $user,
            new Company(
                $command->offer()->company()->name(),
                $command->offer()->company()->url(),
                $command->offer()->company()->description()
            ),
            new Position(
                $command->offer()->position()->seniorityLevel(),
                $command->offer()->position()->name(),
                $command->offer()->position()->description()
            ),
            $location,
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
                $command->offer()->description()->benefits(),
                new Description\Requirements(
                    $command->offer()->description()->requirements()->description(),
                    ...\array_map(
                        function (Requirements\Skill $skill) {
                            return new Description\Requirements\Skill(
                                $skill->skill(),
                                $skill->required(),
                                $skill->experienceYears()
                            );
                        },
                        $command->offer()->description()->requirements()->skills()
                    )
                )
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
