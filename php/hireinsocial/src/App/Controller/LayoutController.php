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

use HireInSocial\Offers;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LayoutController extends AbstractController
{
    private $offers;

    private $templating;

    public function __construct(Offers $offers, EngineInterface $templating)
    {
        $this->offers = $offers;
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
            'headerSpecializations' => $this->offers->specializationQuery()->all(),
        ]);
    }
}
