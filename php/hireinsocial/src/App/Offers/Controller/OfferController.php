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

namespace App\Offers\Controller;

use App\Offers\Form\Type\OfferType;
use Facebook\Facebook;
use HireInSocial\Offers\Application\Command\Offer\Offer\Channels;
use HireInSocial\Offers\Application\Command\Offer\Offer\Company;
use HireInSocial\Offers\Application\Command\Offer\Offer\Contact;
use HireInSocial\Offers\Application\Command\Offer\Offer\Contract;
use HireInSocial\Offers\Application\Command\Offer\Offer\Description;
use HireInSocial\Offers\Application\Command\Offer\Offer\Location;
use HireInSocial\Offers\Application\Command\Offer\Offer\Location\LatLng;
use HireInSocial\Offers\Application\Command\Offer\Offer\Offer;
use HireInSocial\Offers\Application\Command\Offer\Offer\Position;
use HireInSocial\Offers\Application\Command\Offer\Offer\Salary;
use HireInSocial\Offers\Application\Command\Offer\PostOffer;
use HireInSocial\Offers\Application\Command\Offer\RemoveOffer;
use HireInSocial\Offers\Application\Exception\Exception;
use HireInSocial\Offers\Offers;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class OfferController extends AbstractController
{
    use FacebookAccess;
    use RedirectAfterLogin;

    /**
     * @var Offers
     */
    private $offers;

    /**
     * @var Facebook
     */
    private $facebook;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Offers $offers,
        Facebook $facebook,
        LoggerInterface $logger
    ) {
        $this->offers = $offers;
        $this->facebook = $facebook;
        $this->logger = $logger;
    }

    public function postAction(Request $request) : Response
    {
        return $this->render('@offers/offer/post.html.twig', [
            'specializations' => $this->offers->specializationQuery()->all(),
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

        if (!$specialization = $this->offers->specializationQuery()->findBySlug($specSlug)) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(OfferType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $offer = $form->getData();

            try {
                $this->offers->handle(new PostOffer(
                    $specSlug,
                    $userId,
                    new Offer(
                        new Company($offer['company']['name'], $offer['company']['url'], $offer['company']['description']),
                        new Position($offer['position']['name'], $offer['position']['description']),
                        new Location(
                            (bool)$offer['location']['remote'],
                            $offer['location']['name'],
                            ($offer['location']['lat'] && $offer['location']['lng'])
                                ? new LatLng((float) $offer['location']['lat'], (float) $offer['location']['lng'])
                                : null
                        ),
                        (null === $offer['salary']['min'] && null === $offer['salary']['max'])
                            ? null
                            : new Salary($offer['salary']['min'], $offer['salary']['max'], $offer['salary']['currency'], (bool) $offer['salary']['net'], $offer['salary']['period_type']),
                        new Contract($offer['contract']),
                        new Description($offer['description']['requirements'], $offer['description']['benefits']),
                        new Contact($offer['contact']['email'], $offer['contact']['name'], $offer['contact']['phone']),
                        new Channels((bool) $offer['channels']['facebook_group'])
                    ),
                    $offer['offer_pdf'] ? $offer['offer_pdf']->getPathname() : null
                ));

                return $this->redirectToRoute('offer_success', ['specSlug' => $specSlug]);
            } catch (Exception $exception) {
                // TODO: Show some user friendly error message in UI.
                throw $exception;
            }
        }

        return $this->render('@offers/offer/new.html.twig', [
            'specialization' => $specialization,
            'form' => $form->createView(),
            'throttled' => $this->offers->offerThrottleQuery()->isThrottled($userId),
            'offersLeft' => $this->offers->offerThrottleQuery()->offersLeft($userId),
            'throttleLimit' => $this->offers->offerThrottleQuery()->limit(),
            'throttleSince' => $this->offers->offerThrottleQuery()->since(),
            'extraOffersCount' => $this->offers->extraOffersQuery()->countNotExpired($userId),
        ]);
    }

    public function successAction(string $specSlug) : Response
    {
        $specSlug = $this->offers->specializationQuery()->findBySlug($specSlug);

        if (!$specSlug) {
            throw $this->createNotFoundException();
        }

        return $this->render('@offers/offer/success.html.twig', [
            'specialization' => $specSlug,
        ]);
    }

    public function offerAction(Request $request, string $offerSlug) : Response
    {
        $offer = $this->offers->offerQuery()->findBySlug($offerSlug);

        if (!$offer) {
            throw $this->createNotFoundException();
        }

        $facebookPost = $this->offers->facebookPostQuery()->findFacebookPost($offer->id()->toString());

        $nextOffer = $this->offers->offerQuery()->findOneAfter($offer);
        $previousOffer = $this->offers->offerQuery()->findOneBefore($offer);

        return $this->render('@offers/offer/offer.html.twig', [
            'userId' => $request->getSession()->get(FacebookController::USER_SESSION_KEY),
            'offer' => $offer,
            'nextOffer' => $nextOffer,
            'previousOffer' => $previousOffer,
            'facebookPost' => $facebookPost,
        ]);
    }

    public function removeAction(Request $request, string $offerSlug) : Response
    {
        $offer = $this->offers->offerQuery()->findBySlug($offerSlug);
        $userId = $request->getSession()->get(FacebookController::USER_SESSION_KEY);

        if (!$userId || !$offer->postedBy($userId)) {
            return new Response('', Response::HTTP_FORBIDDEN);
        }

        $this->offers->handle(new RemoveOffer($offer->id()->toString(), $userId));

        $this->addFlash('success', $this->renderView('@offers/alert/offer_removed.txt'));

        return $this->redirectToRoute('home');
    }

    public function removeConfirmationAction(Request $request, string $offerSlug) : Response
    {
        $offer = $this->offers->offerQuery()->findBySlug($offerSlug);

        if (!$offer) {
            throw $this->createNotFoundException();
        }

        $facebookPost = $this->offers->facebookPostQuery()->findFacebookPost($offer->id()->toString());


        return $this->render('@offers/offer/remove_confirmation.html.twig', [
            'offerSlug' => $offerSlug,
            'facebookPost' => $facebookPost,
        ]);
    }
}
