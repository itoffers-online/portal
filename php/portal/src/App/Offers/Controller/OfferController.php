<?php

declare(strict_types=1);

/*
 * This file is part of the itoffers.online project.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Offers\Controller;

use App\Offers\Controller\Offer\OfferToForm;
use App\Offers\Form\Type\Offer\ContactType;
use App\Offers\Form\Type\Offer\LocationType;
use App\Offers\Form\Type\OfferType;
use Facebook\Facebook;
use ITOffers\ITOffersOnline;
use ITOffers\Offers\Application\Command\Facebook\PagePostOfferAtGroup;
use ITOffers\Offers\Application\Command\Offer\AssignAutoRenew;
use ITOffers\Offers\Application\Command\Offer\Offer\Company;
use ITOffers\Offers\Application\Command\Offer\Offer\Contact;
use ITOffers\Offers\Application\Command\Offer\Offer\Contract;
use ITOffers\Offers\Application\Command\Offer\Offer\Description;
use ITOffers\Offers\Application\Command\Offer\Offer\Description\Requirements;
use ITOffers\Offers\Application\Command\Offer\Offer\Description\Requirements\Skill;
use ITOffers\Offers\Application\Command\Offer\Offer\Location;
use ITOffers\Offers\Application\Command\Offer\Offer\Location\LatLng;
use ITOffers\Offers\Application\Command\Offer\Offer\Offer;
use ITOffers\Offers\Application\Command\Offer\Offer\Position;
use ITOffers\Offers\Application\Command\Offer\Offer\Salary;
use ITOffers\Offers\Application\Command\Offer\PostOffer;
use ITOffers\Offers\Application\Command\Offer\RemoveOffer;
use ITOffers\Offers\Application\Command\Offer\UpdateOffer;
use ITOffers\Offers\Application\Command\Twitter\TweetAboutOffer;
use ITOffers\Offers\Application\Exception\Exception;
use ITOffers\Offers\Application\FeatureToggle\PostNewOffersFeature;
use ITOffers\Offers\Application\FeatureToggle\PostOfferAtFacebookGroupFeature;
use ITOffers\Offers\Application\FeatureToggle\TweetAboutOfferFeature;
use ITOffers\Offers\UserInterface\OfferThumbnail;
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

    private ITOffersOnline $itoffers;

    private Facebook $facebook;

    private ParameterBagInterface $parameterBag;

    private OfferThumbnail $offerThumbnail;

    private LoggerInterface $logger;

    public function __construct(
        ITOffersOnline $itoffers,
        Facebook $facebook,
        ParameterBagInterface $parameterBag,
        OfferThumbnail $offerThumbnail,
        LoggerInterface $logger
    ) {
        $this->itoffers = $itoffers;
        $this->facebook = $facebook;
        $this->logger = $logger;
        $this->parameterBag = $parameterBag;
        $this->offerThumbnail = $offerThumbnail;
    }

    public function postAction(Request $request) : Response
    {
        if ($this->itoffers->offers()->featureQuery()->isDisabled(PostNewOffersFeature::NAME)) {
            return $this->render('@offers/offer/posting_disabled.html.twig');
        }

        return $this->render('@offers/offer/post.html.twig', [
            'specializations' => $this->itoffers->offers()->specializationQuery()->all(),
        ]);
    }

    public function newAction(string $specSlug, Request $request) : Response
    {
        if ($this->itoffers->offers()->featureQuery()->isDisabled(PostNewOffersFeature::NAME)) {
            return $this->render('@offers/offer/posting_disabled.html.twig');
        }

        if (!$request->getSession()->has(SecurityController::USER_SESSION_KEY)) {
            $this->logger->debug('Not authenticated, redirecting to facebook login.');

            $this->redirectAfterLogin($request->getSession(), 'offer_new', ['specSlug' => $specSlug]);

            return $this->redirectToRoute('login');
        }

        $userId = $request->getSession()->get(SecurityController::USER_SESSION_KEY);

        if (!$specialization = $this->itoffers->offers()->specializationQuery()->findBySlug($specSlug)) {
            throw $this->createNotFoundException();
        }

        try {
            $previousOfferData = $request->query->get('offer-slug')
                ? (new OfferToForm($request->query->get('offer-slug'), $userId))($this->itoffers->offers())
                : null;
        } catch (AccessDeniedException $accessDeniedException) {
            return new Response($accessDeniedException->getMessage(), 403);
        }

        $form = $this->createForm(OfferType::class, $previousOfferData);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $offerData = $form->getData();

            try {
                $this->itoffers->offers()->handle(new PostOffer(
                    $offerId = Uuid::uuid4()->toString(),
                    $specSlug,
                    $offerData['locale'],
                    $userId,
                    $this->createCommandOffer(
                        $offerData,
                        $this->convertOfferDataToLocation($offerData),
                        $this->convertOfferDataToContact($offerData)
                    ),
                    $offerData['offer_pdf'] ? $offerData['offer_pdf']->getPathname() : null
                ));

                $offer = $this->itoffers->offers()->offerQuery()->findById($offerId);

                try {
                    if ($this->itoffers->offers()->featureQuery()->isEnabled(PostOfferAtFacebookGroupFeature::NAME) && (bool)$offerData['channels']['facebook_group']) {
                        $this->itoffers->offers()->handle(new PagePostOfferAtGroup(
                            $offerId,
                            $this->renderView('@offers/facebook/page/group/offer.txt.twig', ['offer' => $offer]),
                        ));
                    }
                } catch (\Throwable $throwable) {
                    $this->logger->critical('Could not post offer at Facebook.', ['class' => \get_class($throwable), 'exception' => $throwable]);
                    $this->addFlash('danger', $this->renderView('@offers/alert/error_post_facebook.txt'));
                }

                try {
                    if ($this->itoffers->offers()->featureQuery()->isEnabled(TweetAboutOfferFeature::NAME) && (bool)$offerData['channels']['twitter']) {
                        $this->itoffers->offers()->handle(new TweetAboutOffer(
                            $offerId,
                            $this->renderView('@offers/twitter/offer.txt.twig', ['offer' => $offer]),
                        ));
                    }
                } catch (\Throwable $throwable) {
                    $this->logger->critical('Could not post offer at Twitter.', ['class' => \get_class($throwable), 'exception' => $throwable]);
                    $this->addFlash('danger', $this->renderView('@offers/alert/error_post_twitter.txt'));
                }

                return $this->redirectToRoute('offer_success', ['specSlug' => $specSlug, 'offer-slug' => $offer->slug()]);
            } catch (Exception $exception) {
                throw $exception;
            }
        }

        return $this->render('@offers/offer/new.html.twig', [
            'specialization' => $specialization,
            'form' => $form->createView(),
            'throttled' => $this->itoffers->offers()->offerThrottleQuery()->isThrottled($userId),
            'offersLeft' => $this->itoffers->offers()->offerThrottleQuery()->offersLeft($userId),
            'throttleLimit' => $this->itoffers->offers()->offerThrottleQuery()->limit(),
            'throttleSince' => $this->itoffers->offers()->offerThrottleQuery()->since(),
            'extraOffersCount' => $this->itoffers->offers()->extraOffersQuery()->countNotExpired($userId),
            'previousOfferData' => $previousOfferData,
            'postOfferAtFacebookGroupEnabled' => $this->itoffers->offers()->featureQuery()->isEnabled(PostOfferAtFacebookGroupFeature::NAME),
            'tweetAboutOfferEnabled' => $this->itoffers->offers()->featureQuery()->isEnabled(TweetAboutOfferFeature::NAME),
            'newOffer' => true,
        ]);
    }

    public function editAction(string $specSlug, Request $request) : Response
    {
        if ($this->itoffers->offers()->featureQuery()->isDisabled(PostNewOffersFeature::NAME)) {
            return $this->render('@offers/offer/posting_disabled.html.twig');
        }

        if (!$request->getSession()->has(SecurityController::USER_SESSION_KEY)) {
            $this->logger->debug('Not authenticated, redirecting to facebook login.');

            $this->redirectAfterLogin($request->getSession(), 'offer_new', ['specSlug' => $specSlug]);

            return $this->redirectToRoute('login');
        }

        $userId = $request->getSession()->get(SecurityController::USER_SESSION_KEY);

        if (!$specialization = $this->itoffers->offers()->specializationQuery()->findBySlug($specSlug)) {
            throw $this->createNotFoundException();
        }

        if (!$request->query->has('offer-slug')) {
            throw $this->createNotFoundException();
        }

        try {
            $offerData = (new OfferToForm($offerSlug = $request->query->get('offer-slug'), $userId))($this->itoffers->offers());
        } catch (AccessDeniedException $accessDeniedException) {
            return new Response($accessDeniedException->getMessage(), 403);
        }
        $offer = $this->itoffers->offers()->offerQuery()->findBySlug($offerSlug);

        $form = $this->createForm(OfferType::class, $offerData);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $offerData = $form->getData();

            try {
                $this->itoffers->offers()->handle(new UpdateOffer(
                    $offer->id()->toString(),
                    $offerData['locale'],
                    $userId,
                    $this->createCommandOffer(
                        $offerData,
                        $this->convertOfferDataToLocation($offerData),
                        $this->convertOfferDataToContact($offerData)
                    ),
                    $offerData['offer_pdf'] ? $offerData['offer_pdf']->getPathname() : null
                ));

                return $this->redirectToRoute('offer_updated', ['specSlug' => $specSlug, 'offer-slug' => $offer->slug()]);
            } catch (Exception $exception) {
                throw $exception;
            }
        }

        return $this->render('@offers/offer/edit.html.twig', [
            'specialization' => $specialization,
            'form' => $form->createView(),
            'offer' => $offer,
            'editOffer' => true,
        ]);
    }

    public function successAction(Request $request, string $specSlug) : Response
    {
        $offer = $this->itoffers->offers()->offerQuery()->findBySlug($request->query->get('offer-slug'));

        if (!$offer) {
            throw $this->createNotFoundException();
        }

        $specSlug = $this->itoffers->offers()->specializationQuery()->findBySlug($specSlug);

        if (!$specSlug) {
            throw $this->createNotFoundException();
        }

        return $this->render('@offers/offer/success.html.twig', [
            'specialization' => $specSlug,
            'offer' => $offer,
        ]);
    }

    public function updatedAction(Request $request, string $specSlug) : Response
    {
        $offer = $this->itoffers->offers()->offerQuery()->findBySlug($request->query->get('offer-slug'));

        if (!$offer) {
            throw $this->createNotFoundException();
        }

        $specSlug = $this->itoffers->offers()->specializationQuery()->findBySlug($specSlug);

        if (!$specSlug) {
            throw $this->createNotFoundException();
        }

        return $this->render('@offers/offer/offer_updated.html.twig', [
            'specialization' => $specSlug,
            'offer' => $offer,
        ]);
    }

    public function offerAction(Request $request, string $offerSlug) : Response
    {
        $offer = $this->itoffers->offers()->offerQuery()->findBySlug($offerSlug);

        if (!$offer) {
            throw $this->createNotFoundException();
        }

        $facebookPost = $this->itoffers->offers()->facebookPostQuery()->findFacebookPost($offer->id()->toString());
        $tweet = $this->itoffers->offers()->tweetsQuery()->findTweet($offer->id()->toString());

        $spcialization = $this->itoffers->offers()->specializationQuery()->findBySlug($offer->specializationSlug());
        $nextOffer = $this->itoffers->offers()->offerQuery()->findOneAfter($offer);
        $previousOffer = $this->itoffers->offers()->offerQuery()->findOneBefore($offer);

        return $this->render('@offers/offer/offer.html.twig', [
            'userId' => $request->getSession()->get(SecurityController::USER_SESSION_KEY),
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
        $offer = $this->itoffers->offers()->offerQuery()->findBySlug($offerSlug);
        $userId = $request->getSession()->get(SecurityController::USER_SESSION_KEY);

        if (!$userId || !$offer->postedBy($userId)) {
            return new Response('', Response::HTTP_FORBIDDEN);
        }

        $this->itoffers->offers()->handle(new RemoveOffer($offer->id()->toString(), $userId));

        $this->addFlash('success', $this->renderView('@offers/alert/offer_removed.txt'));

        return $this->redirectToRoute('home');
    }

    public function assignAutoRenewAction(Request $request, string $offerSlug) : Response
    {
        $offer = $this->itoffers->offers()->offerQuery()->findBySlug($offerSlug);
        $userId = $request->getSession()->get(SecurityController::USER_SESSION_KEY);

        if (!$userId || !$offer->postedBy($userId)) {
            return new Response('', Response::HTTP_FORBIDDEN);
        }

        $this->itoffers->offers()->handle(new AssignAutoRenew(
            $userId,
            $offer->id()->toString()
        ));

        $this->addFlash('success', $this->renderView('@offers/alert/offer_auto_renew_assigned.txt'));


        return $this->redirectToRoute('user_profile');
    }

    public function removeConfirmationAction(Request $request, string $offerSlug) : Response
    {
        $offer = $this->itoffers->offers()->offerQuery()->findBySlug($offerSlug);

        if (!$offer) {
            throw $this->createNotFoundException();
        }

        $facebookPost = $this->itoffers->offers()->facebookPostQuery()->findFacebookPost($offer->id()->toString());
        $tweet = $this->itoffers->offers()->tweetsQuery()->findTweet($offer->id()->toString());
        $specialization = $this->itoffers->offers()->specializationQuery()->findBySlug($offerSlug);

        return $this->render('@offers/offer/remove_confirmation.html.twig', [
            'offer' => $offer,
            'specialization' => $specialization,
            'offerSlug' => $offerSlug,
            'facebookPost' => $facebookPost,
            'tweet' => $tweet,
        ]);
    }

    public function applyAction(Request $request) : Response
    {
        $offer = $this->itoffers->offers()->offerQuery()->findById($request->request->get('offer-id'));

        if ($offer->contact()->isExternalSource()) {
            return new JsonResponse(['url' => $offer->contact()->url()]);
        }

        $email = sprintf($this->parameterBag->get('apply_email_template'), $offer->emailHash());

        return new JsonResponse(['email' => $email]);
    }

    public function thumbnailAction(Request $request, string $offerSlug) : Response
    {
        $offer = $this->itoffers->offers()->offerQuery()->findBySlug($offerSlug);

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

    private function convertOfferDataToLocation(array $offerFormData) : Location
    {
        switch ($offerFormData['location']['type']) {
            case LocationType::LOCATION_PARTIALLY_REMOTE:
                return new Location(
                    true,
                    $offerFormData['location']['country'],
                    $offerFormData['location']['city'],
                    $offerFormData['location']['address'],
                    new LatLng((float)$offerFormData['location']['lat'], (float)$offerFormData['location']['lng'])
                );
            case LocationType::LOCATION_AT_OFFICE:
                return new Location(
                    false,
                    $offerFormData['location']['country'],
                    $offerFormData['location']['city'],
                    $offerFormData['location']['address'],
                    new LatLng((float)$offerFormData['location']['lat'], (float)$offerFormData['location']['lng'])
                );
            default:
                return new Location(true);
        }
    }

    private function convertOfferDataToContact(array $offerFormData) : Contact
    {
        switch ($offerFormData['contact']['type']) {
            case ContactType::RECRUITER_TYPE:
                return Contact::recruiter($offerFormData['contact']['email'], $offerFormData['contact']['name'], $offerFormData['contact']['phone']);
            case ContactType::EXTERNAL_SOURCE_TYPE:
                return Contact::externalSource($offerFormData['contact']['url']);
            default:
                throw new \RuntimeException("Unknown contact type");
        }
    }

    private function createCommandOffer(array $offerFormData, Location $location, Contact $contact) : Offer
    {
        return new Offer(
            new Company(
                $offerFormData['company']['name'],
                $offerFormData['company']['url'],
                $offerFormData['company']['description'],
                $offerFormData['company']['logo'] ? $offerFormData['company']['logo']->getPathname() : null
            ),
            new Position((int)$offerFormData['position']['seniorityLevel'], $offerFormData['position']['name']),
            $location,
            (null === $offerFormData['salary']['min'] && null === $offerFormData['salary']['max'])
                ? null
                : new Salary($offerFormData['salary']['min'], $offerFormData['salary']['max'], $offerFormData['salary']['currency'], (bool)$offerFormData['salary']['net'], $offerFormData['salary']['period_type']),
            new Contract($offerFormData['contract']),
            new Description(
                (string) $offerFormData['description']['technology_stack'],
                $offerFormData['description']['benefits'],
                new Requirements(
                    $offerFormData['description']['requirements']['description'],
                    ...\array_map(
                        fn (array $skillData) => new Skill(
                            $skillData['skill'],
                            (bool)$skillData['required'],
                            $skillData['experience'],
                        ),
                        $offerFormData['description']['requirements']['skills']
                    )
                )
            ),
            $contact
        );
    }
}
