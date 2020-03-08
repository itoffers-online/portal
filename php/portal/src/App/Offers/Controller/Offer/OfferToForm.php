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

namespace App\Offers\Controller\Offer;

use App\Offers\Form\Type\Offer\LocationType;
use ITOffers\Offers\Application\Query\Offer\Model\Offer\Description\Requirements\Skill;
use ITOffers\Offers\Offers;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class OfferToForm
{
    private string $offerSlug;

    private string $userId;

    public function __construct(string $offerSlug, string $userId)
    {
        $this->offerSlug = $offerSlug;
        $this->userId = $userId;
    }

    public function __invoke(Offers $offers) : array
    {
        $offer = $offers->offerQuery()->findBySlug($this->offerSlug);

        if (!$offer) {
            throw new NotFoundHttpException();
        }

        if (!$offer->userId()->equals(Uuid::fromString($this->userId))) {
            throw new AccessDeniedException();
        }

        $locationType = LocationType::LOCATION_REMOTE;

        if ($offer->location()->isPartiallyRemote()) {
            $locationType = LocationType::LOCATION_PARTIALLY_REMOTE;
        }

        if ($offer->location()->isAtOffice()) {
            $locationType = LocationType::LOCATION_AT_OFFICE;
        }

        return [
            'company' => [
                'name' => $offer->company()->name(),
                'url' => $offer->company()->url(),
                'description' => $offer->company()->description(),
            ],
            'position' => [
                'seniorityLevel' => $offer->position()->seniorityLevel(),
                'name' => $offer->position()->name(),
            ],
            'location' => [
                'type' => $locationType,
                'country' => $offer->location()->countryCode(),
                'city' => $offer->location()->city(),
                'lat' => $offer->location()->lat(),
                'lng' => $offer->location()->lng(),
            ],
            'salary' => [
                'min' => $offer->salary() ? $offer->salary()->min() : null,
                'max' => $offer->salary() ? $offer->salary()->max() : null,
                'currency' => $offer->salary() ? $offer->salary()->currencyCode() : null,
                'net' => $offer->salary() ? $offer->salary()->isNet() : null,
                'period_type' => $offer->salary() ? \mb_strtoupper($offer->salary()->periodType()) : null,
            ],
            'contract' => $offer->contract()->type(),
            'description' => [
                'requirements' => [
                    'description' => $offer->description()->requirements()->description(),
                    'skills' => \count($offer->description()->requirements()->skills())
                        ? \array_map(
                            fn (Skill $skill) => [
                                'skill' => $skill->name(),
                                'required' => $skill->required(),
                                'experience' => $skill->experienceYears(),
                            ],
                            $offer->description()->requirements()->skills()
                        ) : [],
                ],
                'technology_stack' => $offer->description()->technologyStack(),
                'benefits' => $offer->description()->benefits(),
            ],
            'contact'=> [
                'name' => $offer->contact()->name(),
                'email' => $offer->contact()->email(),
                'phone' => $offer->contact()->phone(),
            ],
        ];
    }
}
