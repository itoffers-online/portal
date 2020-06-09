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

namespace ITOffers\Offers\Application\Command\Offer;

use Aeon\Calendar\Gregorian\Calendar;
use ITOffers\Component\CQRS\EventStream;
use ITOffers\Component\CQRS\System\Handler;
use ITOffers\Component\Storage\FileStorage;
use ITOffers\Component\Storage\FileStorage\File;
use ITOffers\Offers\Application\Command\Offer\Offer\Description\Requirements\Skill;
use ITOffers\Offers\Application\Exception\Exception;
use ITOffers\Offers\Application\Offer\Company;
use ITOffers\Offers\Application\Offer\CompanyLogo;
use ITOffers\Offers\Application\Offer\CompanyLogos;
use ITOffers\Offers\Application\Offer\Contact;
use ITOffers\Offers\Application\Offer\Contract;
use ITOffers\Offers\Application\Offer\Description;
use ITOffers\Offers\Application\Offer\Description\Requirements;
use ITOffers\Offers\Application\Offer\Event\OfferPostedEvent;
use ITOffers\Offers\Application\Offer\Locale;
use ITOffers\Offers\Application\Offer\Location;
use ITOffers\Offers\Application\Offer\Offer;
use ITOffers\Offers\Application\Offer\OfferPDF;
use ITOffers\Offers\Application\Offer\OfferPDFs;
use ITOffers\Offers\Application\Offer\Offers;
use ITOffers\Offers\Application\Offer\Position;
use ITOffers\Offers\Application\Offer\Salary;
use ITOffers\Offers\Application\Offer\Salary\Period;
use ITOffers\Offers\Application\Offer\Slug;
use ITOffers\Offers\Application\Offer\Slugs;
use ITOffers\Offers\Application\Offer\Throttling;
use ITOffers\Offers\Application\Specialization\Specialization;
use ITOffers\Offers\Application\Specialization\Specializations;
use ITOffers\Offers\Application\User\ExtraOffers;
use ITOffers\Offers\Application\User\User;
use ITOffers\Offers\Application\User\Users;
use Ramsey\Uuid\Uuid;

final class PostOfferHandler implements Handler
{
    private Calendar $calendar;

    private Offers $offers;

    private Users $users;

    private Throttling $throttling;

    private Specializations $specializations;

    private Slugs $slugs;

    private CompanyLogos $companyLogos;

    private OfferPDFs $offerPDFs;

    private FileStorage $fileStorage;

    private ExtraOffers $extraOffers;

    private EventStream $eventStream;

    public function __construct(
        Calendar $calendar,
        Offers $offers,
        ExtraOffers $extraOffers,
        Users $users,
        Throttling $throttling,
        Specializations $specializations,
        Slugs $slugs,
        OfferPDFs $offerPDFs,
        CompanyLogos $companyLogos,
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
        $this->companyLogos = $companyLogos;
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

        if ($command->offer()->company()->logoPath()) {
            $companyLogo = CompanyLogo::forOffer(File::extension($command->offer()->company()->logoPath()), $offer, $slug, $this->calendar);
            $this->fileStorage->upload(File::image($companyLogo->path(), $command->offer()->company()->logoPath()));
            $this->companyLogos->add($companyLogo);
        }

        if ($command->offerPDFPath()) {
            $offerPDF = OfferPDF::forOffer($offer, $slug, $this->calendar);
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
                $command->offer()->location()->address(),
                $command->offer()->location()->latLng()->lat(),
                $command->offer()->location()->latLng()->lng(),
            );
        }

        if (!$command->offer()->location()->remote() && $command->offer()->location()->latLng()) {
            $location = Location::atOffice(
                $command->offer()->location()->countryCode(),
                $command->offer()->location()->city(),
                $command->offer()->location()->address(),
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
                $command->offer()->position()->name()
            ),
            $location,
            $command->offer()->salary()
                ? new Salary(
                    $command->offer()->salary()->min(),
                    $command->offer()->salary()->max(),
                    $command->offer()->salary()->currencyCode(),
                    $command->offer()->salary()->isNet(),
                    Period::fromString(\mb_strtoupper($command->offer()->salary()->periodType()))
                )
                : null,
            new Contract(
                $command->offer()->contract()->type()
            ),
            new Description(
                $command->offer()->description()->technologyStack(),
                $command->offer()->description()->benefits(),
                new Requirements(
                    $command->offer()->description()->requirements()->description(),
                    ...\array_map(
                        fn (Skill $skill) => new Description\Requirements\Skill(
                            $skill->skill(),
                            $skill->required(),
                            $skill->experienceYears()
                        ),
                        $command->offer()->description()->requirements()->skills()
                    )
                )
            ),
            $command->offer()->contact()->url()
                ? Contact::externalSource($command->offer()->contact()->url())
                : Contact::recruiter(
                    $command->offer()->contact()->email(),
                    $command->offer()->contact()->name(),
                    $command->offer()->contact()->phone()
                ),
            $this->calendar
        );
    }
}
