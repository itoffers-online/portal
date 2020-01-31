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

use Facebook\Facebook;
use League\OAuth2\Client\Provider\LinkedIn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SecurityController extends AbstractController
{
    public const USER_SESSION_KEY = '_his_user_id';

    /**
     * @var Facebook
     */
    private $facebook;

    /**
     * @var LinkedIn
     */
    private $linkedIn;

    public function __construct(Facebook $facebook, LinkedIn $linkedIn)
    {
        $this->facebook = $facebook;
        $this->linkedIn = $linkedIn;
    }

    public function userBlockedAction(Request $request) : Response
    {
        return $this->render('@offers/security/user_blocked.html.twig', []);
    }

    public function loginAction(Request $request) : Response
    {
        if ($request->getSession()->has(SecurityController::USER_SESSION_KEY)) {
            return $this->redirectToRoute('home');
        }

        return $this->render('@offers/security/login.html.twig', [
            'facebook_login_url' => $this->facebook->getRedirectLoginHelper()->getLoginUrl(
                $this->generateUrl('facebook_login_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
                ['email']
            ),
            'linkedin_login_url' => $this->linkedIn->getAuthorizationUrl([
                'scope' => ['r_liteprofile','r_emailaddress'],
                'redirect_uri' => $this->generateUrl('linkedin_login_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]),
        ]);
    }

    public function logoutAction(Request $request) : Response
    {
        if ($request->getSession()->has(SecurityController::USER_SESSION_KEY)) {
            $request->getSession()->remove(SecurityController::USER_SESSION_KEY);
        }

        return $this->redirectToRoute('home');
    }
}
