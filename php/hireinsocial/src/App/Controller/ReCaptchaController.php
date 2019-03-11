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

use HireInSocial\Application\Query\Offer\OfferQuery;
use HireInSocial\Application\System;
use ReCaptcha\ReCaptcha;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ReCaptchaController extends AbstractController
{
    private $system;
    private $parameterBag;

    public function __construct(System $system, ParameterBagInterface $parameterBag)
    {
        $this->system = $system;
        $this->parameterBag = $parameterBag;
    }

    public function verifyAction(Request $request) : Response
    {
        $recaptcha = new ReCaptcha($this->parameterBag->get('google_recaptcha_secret'));

        $resp = $recaptcha->setExpectedHostname($request->getHttpHost())
            ->verify($request->request->get('google-recaptcha-token'), $request->getClientIp());

        $offer = $this->system->query(OfferQuery::class)->findById($request->request->get('offer-id'));
        $email = \sprintf($this->parameterBag->get('apply_email_template'), $offer->slug());

        if ($resp->isSuccess()) {
            return new JsonResponse(['email' => $email]);
        } else {
            return new JsonResponse([], 422);
        }
    }
}
