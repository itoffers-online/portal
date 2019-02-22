<?php

declare(strict_types=1);

namespace HireInSocial\UserInterface\Symfony\Controller;

use HireInSocial\Application\Query\Offer\OfferFilter;
use HireInSocial\Application\Query\Offer\OfferQuery;
use HireInSocial\Application\Query\Specialization\SpecializationQuery;
use HireInSocial\Application\System;
use HireInSocial\UserInterface\Symfony\Form\Type\OfferFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SpecializationController extends AbstractController
{

    private $system;
    private $templating;

    public function __construct(System $system, EngineInterface $templating)
    {
        $this->system = $system;
        $this->templating = $templating;
    }

    public function offersAction(Request $request, string $specSlug) : Response
    {
        $specialization = $this->system->query(SpecializationQuery::class)->findBySlug($specSlug);

        if (!$specialization) {
            throw $this->createNotFoundException();
        }


        $offerFilter = OfferFilter::allFor($specialization->slug())
            ->changeSlice(20, 0);

        $form = $this->createForm(OfferFilterType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            var_dump($form->getData());
            die();
        }

        $offers = $this->system
            ->query(OfferQuery::class)
            ->findAll($offerFilter);

        return $this->templating->renderResponse('/specialization/offers.html.twig', [
            'total' => $this->system->query(OfferQuery::class)->count($offerFilter),
            'specialization' => $specialization,
            'offers' => $offers,
            'form' => $form->createView()
        ]);
    }
}
