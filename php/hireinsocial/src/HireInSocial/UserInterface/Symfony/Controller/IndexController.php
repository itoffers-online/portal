<?php

declare(strict_types=1);

namespace HireInSocial\UserInterface\Symfony\Controller;

use HireInSocial\Application\Query\Offer\OfferFilter;
use HireInSocial\Application\Query\Offer\OfferQuery;
use HireInSocial\Application\Query\Specialization\SpecializationQuery;
use HireInSocial\Application\System;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class IndexController extends AbstractController
{
    use ControllerTrait;

    private $system;
    private $templating;

    public function __construct(System $system, EngineInterface $templating)
    {
        $this->system = $system;
        $this->templating = $templating;
    }

    public function homeAction(Request $request) : Response
    {
        $offerFilter = OfferFilter::all()
            ->changeSlice(50, 0);

        $offers = $this->system
            ->query(OfferQuery::class)
            ->findAll($offerFilter);

        return $this->templating->renderResponse('/home/index.html.twig', [
            'specializations' => $this->system->query(SpecializationQuery::class)->all(),
            'offers' => $offers,
        ]);
    }

    public function faqAction(Request $request) : Response
    {
        return $this->templating->renderResponse('/home/faq.html.twig', []);
    }
}
