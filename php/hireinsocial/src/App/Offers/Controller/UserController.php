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

use HireInSocial\Offers\Application\Query\Offer\OfferFilter;
use HireInSocial\Offers\Offers;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UserController extends AbstractController
{
    use RedirectAfterLogin;

    /**
     * @var Offers
     */
    private $offers;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(Offers $offers, LoggerInterface $logger)
    {
        $this->offers = $offers;
        $this->logger = $logger;
    }

    public function profileAction(Request $request) : Response
    {
        if (!$request->getSession()->has(FacebookController::USER_SESSION_KEY)) {
            $this->logger->debug('Not authenticated, redirecting to facebook login.');

            $this->redirectAfterLogin($request->getSession(), 'user_profile');

            return $this->redirectToRoute('facebook_login');
        }

        $userId = $request->getSession()->get(FacebookController::USER_SESSION_KEY);

        /** @var OfferFilter $offerFilter */
        $offerFilter = OfferFilter::all()
            ->belongsTo($userId)
            ->changeSize(20, 0);

        $offers = $this->offers->offerQuery()->findAll($offerFilter);

        return $this->render('@offers/user/profile.html.twig', [
            'user' => $this->offers->userQuery()->findById($userId),
            'offersLeft' => $this->offers->offerThrottleQuery()->offersLeft($userId),
            'extraOffersCount' => $this->offers->extraOffersQuery()->countNotExpired($userId),
            'extraOffer' => $this->offers->extraOffersQuery()->findClosesToExpire($userId),
            'offers' => $offers,
        ]);
    }
}
