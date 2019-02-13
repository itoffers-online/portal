<?php

declare(strict_types=1);

namespace HireInSocial\UserInterface\Symfony\Controller;

use Facebook\Authentication\AccessToken;
use Facebook\Facebook;
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
use HireInSocial\Application\Query\Offer\OfferQuery;
use HireInSocial\Application\Query\Offer\OfferThrottleQuery;
use HireInSocial\Application\Query\Specialization\SpecializationQuery;
use HireInSocial\Application\System;
use HireInSocial\UserInterface\Symfony\Form\Type\OfferType;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class OfferController extends AbstractController
{
    use FacebookAccess;

    private $facebook;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(Facebook $facebook, LoggerInterface $logger)
    {
        $this->facebook = $facebook;
        $this->logger = $logger;
    }

    public function newAction(string $specialization, Request $request) : Response
    {
        try {
            $fbUserId = $this->getUserId(
                $this->facebook,
                new AccessToken((string)$request->getSession()->get(FacebookController::FACEBOOK_USER_TOKEN_SESSION_KEY)),
                $this->logger
            );
        } catch (\Throwable $exception) {
            $this->logger->debug('Not authenticated, redirecting to facebook login.', ['exception' => $exception->getMessage()]);

            return $this->redirectToRoute('facebook_login');
        }

        if (!$this->get(System::class)->query(SpecializationQuery::class)->findBySlug($specialization)) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(OfferType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $offer = $form->getData();

            try {
                $this->container->get(System::class)->handle(new PostToGroup(
                    $specialization,
                    $fbUserId,
                    new Offer(
                        new Company($offer['company']['name'], $offer['company']['url'], $offer['company']['description']),
                        new Position($offer['position']['name'], $offer['position']['description']),
                        new Location((bool)$offer['location']['remote'], $offer['location']['name']),
                        (null === $offer['salary']['min'] && null === $offer['salary']['max'])
                            ? null
                            : new Salary($offer['salary']['min'], $offer['salary']['max'], $offer['salary']['currency'], (bool)$offer['salary']['net']),
                        new Contract($offer['contract']),
                        new Description($offer['description']['requirements'], $offer['description']['benefits']),
                        new Contact($offer['contact']['email'], $offer['contact']['name'], $offer['contact']['phone'])
                    )
                ));

                return $this->redirectToRoute('offer_success', ['specialization' => $specialization]);
            } catch (Exception $exception) {
                throw $exception;
            }
        }

        return $this->render('/offer/new.html.twig', [
            'form' => $form->createView(),
            'throttled' => $this->get(System::class)->query(OfferThrottleQuery::class)->isThrottled($fbUserId),
        ]);
    }

    public function successAction(string $specialization) : Response
    {
        $specialization = $this->get(System::class)->query(SpecializationQuery::class)->findBySlug($specialization);

        if (!$specialization) {
            throw $this->createNotFoundException();
        }

        return $this->render('/offer/success.html.twig', [
            'specialization' => $specialization,
        ]);
    }

    public function offerAction(string $slug) : Response
    {
        $offer = $this->get(System::class)->query(OfferQuery::class)->findBySlug($slug);

        if (!$offer) {
            throw $this->createNotFoundException();
        }

        return $this->render('offer/offer.html.twig', [
            'offer' => $offer,
        ]);
    }
}
