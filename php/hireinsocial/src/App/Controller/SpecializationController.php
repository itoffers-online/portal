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

namespace App\Controller;

use App\Form\Type\OfferFilterType;
use HireInSocial\Application\Query\Offer\OfferFilter;
use HireInSocial\Application\Query\Offer\OfferQuery;
use HireInSocial\Application\Query\Specialization\SpecializationQuery;
use HireInSocial\Application\System;
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

        $offerFilter = OfferFilter::allFor($specialization->slug());

        $form = $this->createForm(OfferFilterType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('remote')->getData()) {
                $offerFilter->onlyRemote();
            }

            if ($form->get('with_salary')->getData()) {
                $offerFilter->onlyWithSalary();
            }

            if ($sortBy = $form->get('sort_by')->getData()) {
                $offerFilter->sortBy($sortBy);
            }
        }

        $offers = $this->system
            ->query(OfferQuery::class)
            ->findAll($offerFilter->changeSize(20, 0));

        return $this->templating->renderResponse('/specialization/offers.html.twig', [
            'total' => $this->system->query(OfferQuery::class)->count($offerFilter),
            'specialization' => $specialization,
            'offers' => $offers,
            'form' => $form->createView(),
            'queryParameters' => $request->query->all(),
        ]);
    }
}
