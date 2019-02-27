<?php

declare(strict_types=1);

namespace App\Controller;

use HireInSocial\Application\Query\Specialization\SpecializationQuery;
use HireInSocial\Application\System;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LayoutController extends AbstractController
{
    private $system;
    private $templating;

    public function __construct(System $system, EngineInterface $templating)
    {
        $this->system = $system;
        $this->templating = $templating;
    }

    public function navbarAction(Request $request) : Response
    {
        return $this->templating->renderResponse('layout/navbar.html.twig', [
            'facebook_logged_in' => (bool) $request->getSession()->get(FacebookController::USER_SESSION_KEY, false),
        ]);
    }

    public function headerAction(Request $request) : Response
    {
        return $this->templating->renderResponse('layout/header.html.twig', [
            'headerSpecializations' => $this->system->query(SpecializationQuery::class)->all(),
        ]);
    }
}
