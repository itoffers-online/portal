<?php

declare(strict_types=1);

namespace HireInSocial\UserInterface\Symfony\Controller;

use HireInSocial\Application\Query\Offer\OfferFilter;
use HireInSocial\Application\Query\Offer\OfferQuery;
use HireInSocial\Application\Query\Specialization\SpecializationQuery;
use HireInSocial\Application\System;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class SpecializationController extends AbstractController
{
    public function offersAction(string $slug) : Response
    {
        $specialization = $this->get(System::class)->query(SpecializationQuery::class)->findBySlug($slug);

        if (!$specialization) {
            throw $this->createNotFoundException();
        }

        $offerFilter = OfferFilter::allFor($specialization->slug())
            ->changeSlice(50, 0);

        $offers = $this->get(System::class)
            ->query(OfferQuery::class)
            ->findAll($offerFilter);

        return $this->render('/specialization/offers.html.twig', [
            'total' => $this->get(System::class)->query(OfferQuery::class)->count($offerFilter),
            'specialization' => $specialization,
            'offers' => $offers,
        ]);
    }
}
