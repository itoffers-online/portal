<?php

declare(strict_types=1);

namespace HireInSocial\UserInterface\Symfony\Controller;

use HireInSocial\Application\Command\Facebook\Page\PostToGroup;
use HireInSocial\Application\Command\Offer\Company;
use HireInSocial\Application\Command\Offer\Contact;
use HireInSocial\Application\Command\Offer\Contract;
use HireInSocial\Application\Command\Offer\Description;
use HireInSocial\Application\Command\Offer\Location;
use HireInSocial\Application\Command\Offer\Offer;
use HireInSocial\Application\Command\Offer\Position;
use HireInSocial\Application\Command\Offer\Salary;
use HireInSocial\Application\Exception\Exception;
use HireInSocial\Application\Query\Offer\OfferThrottleQuery;
use HireInSocial\Application\System;
use HireInSocial\UserInterface\Symfony\Form\Type\OfferType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class OfferController extends AbstractController
{
    public function newAction(Request $request) : Response
    {
        if (!$request->getSession()->has(FacebookController::FACEBOOK_ID_SESSION_KEY)) {
            return $this->redirectToRoute('facebook_login');
        }

        $fbUserId = $request->getSession()->get(FacebookController::FACEBOOK_ID_SESSION_KEY);

        $form = $this->createForm(OfferType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $offer = $form->getData();

            try {
                $this->container->get(System::class)->handle(new PostToGroup(
                    $fbUserId,
                    new Offer(
                        new Company($offer['company']['name'], $offer['company']['url'], $offer['company']['description']),
                        new Position($offer['position']['name'], $offer['position']['description']),
                        new Location((bool)$offer['location']['remote'], $offer['location']['name']),
                        new Salary($offer['salary']['min'], $offer['salary']['max'], $offer['salary']['currency'], (bool)$offer['salary']['net']),
                        new Contract($offer['contract']),
                        new Description($offer['description']['requirements'], $offer['description']['benefits']),
                        new Contact($offer['contact']['email'], $offer['contact']['name'], $offer['contact']['phone'])
                    )
                ));

                return $this->redirectToRoute('offer_success');
            } catch (Exception $exception) {
                throw $exception;
            }
        }

        return $this->render('/offer/new.html.twig', [
            'form' => $form->createView(),
            'throttled' => $this->get(System::class)->query(OfferThrottleQuery::class)->isThrottled($fbUserId),
        ]);
    }

    public function successAction() : Response
    {
        return $this->render('/offer/success.html.twig');
    }
}
