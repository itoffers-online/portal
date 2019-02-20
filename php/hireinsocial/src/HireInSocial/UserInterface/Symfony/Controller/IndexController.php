<?php

declare(strict_types=1);

namespace HireInSocial\UserInterface\Symfony\Controller;

use HireInSocial\Application\Query\Offer\OfferFilter;
use HireInSocial\Application\Query\Offer\OfferQuery;
use HireInSocial\Application\Query\Specialization\SpecializationQuery;
use HireInSocial\Application\System;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class IndexController extends AbstractController
{
    public function homeAction(Request $request) : Response
    {
        $offerFilter = OfferFilter::all()
            ->changeSlice(50, 0);

        $offers = $this->get(System::class)
            ->query(OfferQuery::class)
            ->findAll($offerFilter);

        return $this->render('/home/index.html.twig', [
            'specializations' => $this->get(System::class)->query(SpecializationQuery::class)->all(),
            'offers' => $offers,
        ]);
    }

    public function faqAction(Request $request) : Response
    {
        return $this->render('/home/faq.html.twig', []);
    }
}
