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

use ITOffers\ITOffersOnline;
use ITOffers\Offers\Application\Query\Offer\OfferFilter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UserController extends AbstractController
{
    use RedirectAfterLogin;

    /**
     * @var ITOffersOnline
     */
    private $itoffers;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ITOffersOnline $itoffers, LoggerInterface $logger)
    {
        $this->itoffers = $itoffers;
        $this->logger = $logger;
    }

    public function profileAction(Request $request) : Response
    {
        if (!$request->getSession()->has(SecurityController::USER_SESSION_KEY)) {
            $this->logger->debug('Not authenticated, redirecting to facebook login.');

            $this->redirectAfterLogin($request->getSession(), 'user_profile');

            return $this->redirectToRoute('login');
        }

        $userId = $request->getSession()->get(SecurityController::USER_SESSION_KEY);

        /** @var OfferFilter $offerFilter */
        $offerFilter = OfferFilter::all()
            ->belongsTo($userId)
            ->max(5);

        $total = $this->itoffers->offers()->offerQuery()->count($offerFilter);

        if ($request->query->has('after')) {
            $offerFilter->showAfter($request->query->get('after'));
        }

        $offers = $this->itoffers->offers()->offerQuery()->findAll($offerFilter);
        $offerMore = $this->itoffers->offers()->offerQuery()->count($offerFilter);

        return $this->render('@offers/user/profile.html.twig', [
            'showingOlder' => $request->query->has('after'),
            'user' => $this->itoffers->offers()->userQuery()->findById($userId),
            'offersMore' => $offerMore,
            'offersLeft' => $this->itoffers->offers()->offerThrottleQuery()->offersLeft($userId),
            'offerAutoRenewsCount' => $this->itoffers->offers()->offerAutoRenewQuery()->countUnassignedNotExpired($userId),
            'extraOffersCount' => $this->itoffers->offers()->extraOffersQuery()->countNotExpired($userId),
            'extraOffer' => $this->itoffers->offers()->extraOffersQuery()->findClosesToExpire($userId),
            'offers' => $offers,
            'totalOffers' => $total,
        ]);
    }
}
