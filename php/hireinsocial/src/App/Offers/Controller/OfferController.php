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

use App\Offers\Controller\Offer\OfferToForm;
use App\Offers\Form\Type\OfferType;
use Facebook\Facebook;
use HireInSocial\Offers\Application\Command\Facebook\PagePostOfferAtGroup;
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
use HireInSocial\Offers\Application\Command\Twitter\TweetAboutOffer;
use HireInSocial\Offers\Application\Exception\Exception;
use HireInSocial\Offers\Application\FeatureToggle\PostNewOffersFeature;
use HireInSocial\Offers\Offers;
use HireInSocial\Offers\UserInterface\OfferThumbnail;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @var OfferThumbnail
     */
    private $offerThumbnail;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Offers $offers,
        Facebook $facebook,
        ParameterBagInterface $parameterBag,
        OfferThumbnail $offerThumbnail,
        LoggerInterface $logger
    ) {
        $this->offers = $offers;
        $this->facebook = $facebook;
        $this->logger = $logger;
        $this->parameterBag = $parameterBag;
        $this->offerThumbnail = $offerThumbnail;
    }

    public function postAction(Request $request) : Response
    {
        if ($this->offers->featureQuery()->isDisabled(PostNewOffersFeature::NAME)) {
            return $this->render('@offers/offer/posting_disabled.html.twig');
        }

        return $this->render('@offers/offer/post.html.twig', [
            'specializations' => $this->offers->specializationQuery()->all(),
        ]);
    }

    public function newAction(string $specSlug, Request $request) : Response
    {
        if ($this->offers->featureQuery()->isDisabled(PostNewOffersFeature::NAME)) {
            return $this->render('@offers/offer/posting_disabled.html.twig');
        }

        if (!$request->getSession()->has(FacebookController::USER_SESSION_KEY)) {
            $this->logger->debug('Not authenticated, redirecting to facebook login.');

            $this->redirectAfterLogin($request->getSession(), 'offer_new', ['specSlug' => $specSlug]);

            return $this->redirectToRoute('facebook_login');
        }

        $userId = $request->getSession()->get(FacebookController::USER_SESSION_KEY);

        if (!$specialization = $this->offers->specializationQuery()->findBySlug($specSlug)) {
            throw $this->createNotFoundException();
        }

        try {
            $previousOfferData = $request->query->get('offer-slug')
                ? (new OfferToForm($request->query->get('offer-slug'), $userId))($this->offers)
                : null;
        } catch (AccessDeniedException $accessDeniedException) {
            return new Response($accessDeniedException->getMessage(), 403);
        }

        $form = $this->createForm(OfferType::class, $previousOfferData);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $offerData = $form->getData();

            try {
                switch ($offerData['location']['type']) {
                    case 1:
                    case 2:
                        $location = new Location(
                            true,
                            $offerData['location']['country'],
                            $offerData['location']['city'],
                            new LatLng((float) $offerData['location']['lat'], (float) $offerData['location']['lng'])
                        );

                        break;
                    default:
                        $location = new Location(true);

                        break;
                }

                $this->offers->handle(new PostOffer(
                    $offerId = Uuid::uuid4()->toString(),
                    $specSlug,
                    $offerData['locale'],
                    $userId,
                    new Offer(
                        new Company($offerData['company']['name'], $offerData['company']['url'], $offerData['company']['description']),
                        new Position($offerData['position']['seniorityLevel'], $offerData['position']['name'], $offerData['position']['description']),
                        $location,
                        (null === $offerData['salary']['min'] && null === $offerData['salary']['max'])
                            ? null
                            : new Salary($offerData['salary']['min'], $offerData['salary']['max'], $offerData['salary']['currency'], (bool) $offerData['salary']['net'], $offerData['salary']['period_type']),
                        new Contract($offerData['contract']),
                        new Description($offerData['description']['requirements'], $offerData['description']['benefits']),
                        new Contact($offerData['contact']['email'], $offerData['contact']['name'], $offerData['contact']['phone']),
                    ),
                    $offerData['offer_pdf'] ? $offerData['offer_pdf']->getPathname() : null
                ));

                $offer = $this->offers->offerQuery()->findById($offerId);

                if ((bool) $offerData['channels']['facebook_group']) {
                    $this->offers->handle(new PagePostOfferAtGroup(
                        $offerId,
                        $this->renderView('@offers/facebook/page/group/offer.txt.twig', ['offer' => $offer]),
                    ));
                }

                if ((bool) $offerData['channels']['twitter']) {
                    $this->offers->handle(new TweetAboutOffer(
                        $offerId,
                        $this->renderView('@offers/twitter/offer.txt.twig', ['offer' => $offer]),
                    ));
                }

                return $this->redirectToRoute('offer_success', ['specSlug' => $specSlug, 'offer-slug' => $offer->slug()]);
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
            'previousOfferData' => $previousOfferData,
        ]);
    }

    public function successAction(Request $request, string $specSlug) : Response
    {
        $offer = $this->offers->offerQuery()->findBySlug($request->query->get('offer-slug'));

        if (!$offer) {
            throw $this->createNotFoundException();
        }

        $specSlug = $this->offers->specializationQuery()->findBySlug($specSlug);

        if (!$specSlug) {
            throw $this->createNotFoundException();
        }

        return $this->render('@offers/offer/success.html.twig', [
            'specialization' => $specSlug,
            'offer' => $offer,
        ]);
    }

    public function offerAction(Request $request, string $offerSlug) : Response
    {
        $offer = $this->offers->offerQuery()->findBySlug($offerSlug);

        if (!$offer) {
            throw $this->createNotFoundException();
        }

        $facebookPost = $this->offers->facebookPostQuery()->findFacebookPost($offer->id()->toString());
        $tweet = $this->offers->tweetsQuery()->findTweet($offer->id()->toString());

        $spcialization = $this->offers->specializationQuery()->findBySlug($offer->specializationSlug());
        $nextOffer = $this->offers->offerQuery()->findOneAfter($offer);
        $previousOffer = $this->offers->offerQuery()->findOneBefore($offer);

        return $this->render('@offers/offer/offer.html.twig', [
            'userId' => $request->getSession()->get(FacebookController::USER_SESSION_KEY),
            'offer' => $offer,
            'nextOffer' => $nextOffer,
            'previousOffer' => $previousOffer,
            'facebookPost' => $facebookPost,
            'tweet' => $tweet,
            'specialization' => $spcialization,
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

    public function applyAction(Request $request) : Response
    {
        $offer = $this->offers->offerQuery()->findById($request->request->get('offer-id'));
        $email = sprintf($this->parameterBag->get('apply_email_template'), $offer->emailHash());

        return new JsonResponse(['email' => $email]);
    }

    public function thumbnailAction(Request $request, string $offerSlug) : Response
    {
        $offer = $this->offers->offerQuery()->findBySlug($offerSlug);

        if (!$offer) {
            throw $this->createNotFoundException();
        }

        $thumbnailPath = $this->offerThumbnail->large($offer, false);

        $response = new Response();
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, \basename($thumbnailPath));
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'image/png');
        $response->setContent(\file_get_contents($thumbnailPath));

        return $response;
    }
}
