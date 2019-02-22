<?php

declare(strict_types=1);

namespace HireInSocial\UserInterface\Symfony\Controller;

use Facebook\Facebook;
use HireInSocial\Application\Command\Offer\PostOffer;
use HireInSocial\Application\Command\Offer\Offer\Channels;
use HireInSocial\Application\Command\Offer\Offer\Company;
use HireInSocial\Application\Command\Offer\Offer\Contact;
use HireInSocial\Application\Command\Offer\Offer\Contract;
use HireInSocial\Application\Command\Offer\Offer\Description;
use HireInSocial\Application\Command\Offer\Offer\Location;
use HireInSocial\Application\Command\Offer\Offer\Offer;
use HireInSocial\Application\Command\Offer\Offer\Position;
use HireInSocial\Application\Command\Offer\Offer\Salary;
use HireInSocial\Application\Exception\Exception;
use HireInSocial\Application\Query\Offer\OfferQuery;
use HireInSocial\Application\Query\Offer\OfferThrottleQuery;
use HireInSocial\Application\Query\Specialization\SpecializationQuery;
use HireInSocial\Application\System;
use HireInSocial\UserInterface\Symfony\Form\Type\OfferType;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class OfferController extends AbstractController
{
    use FacebookAccess;
    use RedirectAfterLogin;

    private $system;
    private $templating;
    private $facebook;
    private $logger;

    public function __construct(System $system, EngineInterface $templating, Facebook $facebook, LoggerInterface $logger)
    {
        $this->system = $system;
        $this->templating = $templating;
        $this->facebook = $facebook;
        $this->logger = $logger;
    }

    public function postAction(Request $request) : Response
    {
        return $this->templating->renderResponse('/offer/post.html.twig', [
            'specializations' => $this->system->query(SpecializationQuery::class)->all(),
        ]);
    }

    public function newAction(string $specSlug, Request $request) : Response
    {
        if (!$request->getSession()->has(FacebookController::USER_SESSION_KEY)) {
            $this->logger->debug('Not authenticated, redirecting to facebook login.');

            $this->redirectAfterLogin($request->getSession(), 'offer_new', ['specSlug' => $specSlug]);

            return $this->redirectToRoute('facebook_login');
        }

        $userId = $request->getSession()->get(FacebookController::USER_SESSION_KEY);

        if (!$this->system->query(SpecializationQuery::class)->findBySlug($specSlug)) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(OfferType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $offer = $form->getData();

            try {
                $this->system->handle(new PostOffer(
                    $specSlug,
                    $userId,
                    new Offer(
                        new Company($offer['company']['name'], $offer['company']['url'], $offer['company']['description']),
                        new Position($offer['position']['name'], $offer['position']['description']),
                        new Location((bool)$offer['location']['remote'], $offer['location']['name']),
                        (null === $offer['salary']['min'] && null === $offer['salary']['max'])
                            ? null
                            : new Salary($offer['salary']['min'], $offer['salary']['max'], $offer['salary']['currency'], (bool)$offer['salary']['net']),
                        new Contract($offer['contract']),
                        new Description($offer['description']['requirements'], $offer['description']['benefits']),
                        new Contact($offer['contact']['email'], $offer['contact']['name'], $offer['contact']['phone']),
                        new Channels((bool) $offer['channels']['facebook_group'])
                    )
                ));

                return $this->redirectToRoute('offer_success', ['specSlug' => $specSlug]);
            } catch (Exception $exception) {
                // TODO: Show some user friendly error message in UI.
                throw $exception;
            }
        }

        return $this->templating->renderResponse('/offer/new.html.twig', [
            'form' => $form->createView(),
            'throttled' => $this->system->query(OfferThrottleQuery::class)->isThrottled($userId),
        ]);
    }

    public function successAction(string $specSlug) : Response
    {
        $specSlug = $this->system->query(SpecializationQuery::class)->findBySlug($specSlug);

        if (!$specSlug) {
            throw $this->createNotFoundException();
        }

        return $this->templating->renderResponse('/offer/success.html.twig', [
            'specialization' => $specSlug,
        ]);
    }

    public function offerAction(string $offerSlug) : Response
    {
        $offer = $this->system->query(OfferQuery::class)->findBySlug($offerSlug);

        if (!$offer) {
            throw $this->createNotFoundException();
        }

        $nextOffer = $this->system->query(OfferQuery::class)->findOneAfter($offer);
        $previousOffer = $this->system->query(OfferQuery::class)->findOneBefore($offer);

        return $this->templating->renderResponse('offer/offer.html.twig', [
            'offer' => $offer,
            'nextOffer' => $nextOffer,
            'previousOffer' => $previousOffer,
        ]);
    }
}
