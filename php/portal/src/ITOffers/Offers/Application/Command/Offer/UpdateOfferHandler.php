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

use ITOffers\Component\Calendar\Calendar;
use ITOffers\Component\CQRS\System\Handler;
use ITOffers\Component\Storage\FileStorage;
use ITOffers\Component\Storage\FileStorage\File;
use ITOffers\Offers\Application\Command\Offer\Offer\Description\Requirements\Skill;
use ITOffers\Offers\Application\Offer\Company;
use ITOffers\Offers\Application\Offer\CompanyLogo;
use ITOffers\Offers\Application\Offer\CompanyLogos;
use ITOffers\Offers\Application\Offer\Contact;
use ITOffers\Offers\Application\Offer\Contract;
use ITOffers\Offers\Application\Offer\Description;
use ITOffers\Offers\Application\Offer\Description\Requirements;
use ITOffers\Offers\Application\Offer\Locale;
use ITOffers\Offers\Application\Offer\Location;
use ITOffers\Offers\Application\Offer\OfferPDF;
use ITOffers\Offers\Application\Offer\OfferPDFs;
use ITOffers\Offers\Application\Offer\Offers;
use ITOffers\Offers\Application\Offer\Position;
use ITOffers\Offers\Application\Offer\Salary;
use ITOffers\Offers\Application\Offer\Salary\Period;
use ITOffers\Offers\Application\Offer\Slugs;
use ITOffers\Offers\Application\User\Users;
use Ramsey\Uuid\Uuid;

final class UpdateOfferHandler implements Handler
{
    private Calendar $calendar;

    private Offers $offers;

    private Slugs $slugs;

    private Users $users;

    private OfferPDFs $offerPDFs;

    private CompanyLogos $companyLogos;

    private FileStorage $fileStorage;

    public function __construct(
        Calendar $calendar,
        Offers $offers,
        Slugs $slugs,
        Users $users,
        OfferPDFs $offerPDFs,
        CompanyLogos $companyLogos,
        FileStorage $fileStorage
    ) {
        $this->calendar = $calendar;
        $this->offers = $offers;
        $this->users = $users;
        $this->offerPDFs = $offerPDFs;
        $this->fileStorage = $fileStorage;
        $this->companyLogos = $companyLogos;
        $this->slugs = $slugs;
    }

    public function handles() : string
    {
        return UpdateOffer::class;
    }

    public function __invoke(UpdateOffer $command) : void
    {
        $user = $this->users->getById(Uuid::fromString($command->userId()));
        $offer = $this->offers->getById(Uuid::fromString($command->offerId()));
        $slug = $this->slugs->getById($offer->id());

        $offer->update(
            $user,
            new Locale($command->locale()),
            new Company(
                $command->offer()->company()->name(),
                $command->offer()->company()->url(),
                $command->offer()->company()->description()
            ),
            new Position(
                $command->offer()->position()->seniorityLevel(),
                $command->offer()->position()->name()
            ),
            $this->createLocation($command),
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

        if ($command->offer()->company()->logoPath()) {
            $this->companyLogos->removeFor($offer->id());

            $companyLogo = CompanyLogo::forOffer(File::extension($command->offer()->company()->logoPath()), $offer, $slug, $this->calendar);
            $this->fileStorage->upload(File::image($companyLogo->path(), $command->offer()->company()->logoPath()));
            $this->companyLogos->add($companyLogo);
        }

        if ($command->offerPDFPath()) {
            $this->offerPDFs->removeFor($offer->id());

            $offerPDF = OfferPDF::forOffer($offer, $slug, $this->calendar);
            $this->fileStorage->upload(File::pdf($offerPDF->path(), $command->offerPDFPath()));
            $this->offerPDFs->add($offerPDF);
        }
    }

    private function createLocation(UpdateOffer $command) : Location
    {
        $location = Location::remote();

        if ($command->offer()->location()->remote() && $command->offer()->location()->latLng()) {
            $location = Location::partiallyRemote(
                $command->offer()->location()->countryCode(),
                $command->offer()->location()->city(),
                $command->offer()->location()->address(),
                $command->offer()->location()->latLng()->lat(),
                $command->offer()->location()->latLng()->lng()
            );
        }

        if (!$command->offer()->location()->remote() && $command->offer()->location()->latLng()) {
            $location = Location::atOffice(
                $command->offer()->location()->countryCode(),
                $command->offer()->location()->city(),
                $command->offer()->location()->address(),
                $command->offer()->location()->latLng()->lat(),
                $command->offer()->location()->latLng()->lng()
            );
        }

        return $location;
    }
}
