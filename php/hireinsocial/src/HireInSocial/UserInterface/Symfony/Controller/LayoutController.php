<?php

declare(strict_types=1);

namespace HireInSocial\UserInterface\Symfony\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LayoutController extends AbstractController
{
    public function navbarAction(Request $request) : Response
    {
        return $this->render('layout/navbar.html.twig', [
            'facebook_logged_in' => (bool) $request->getSession()->get(FacebookController::USER_SESSION_KEY, false),
        ]);
    }
}
