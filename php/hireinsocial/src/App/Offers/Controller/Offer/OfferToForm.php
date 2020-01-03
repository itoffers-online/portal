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

namespace App\Offers\Controller\Offer;

use HireInSocial\Offers\Offers;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class OfferToForm
{
    /**
     * @var string
     */
    private $offerSlug;

    /**
     * @var string
     */
    private $userId;

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

        return [
            'company' => [
                'name' => $offer->company()->name(),
                'url' => $offer->company()->url(),
                'description' => $offer->company()->description(),
            ],
            'position' => [
                'seniorityLevel' => $offer->position()->seniorityLevel(),
                'name' => $offer->position()->name(),
                'description' => $offer->position()->description(),
            ],
            'location' => [
                'remote' => $offer->location()->remote(),
                'name' => $offer->location()->name(),
                'lat' => $offer->location()->lat(),
                'lng' => $offer->location()->lng(),
            ],
            'salary' => [
                'min' => $offer->salary()->min(),
                'max' => $offer->salary()->max(),
                'currency' => $offer->salary()->currencyCode(),
                'net' => $offer->salary()->isNet(),
                'periodType' => $offer->salary()->periodType(),
            ],
            'contract' => $offer->contract()->type(),
            'description' => [
                'requirements' => $offer->description()->requirements(),
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
