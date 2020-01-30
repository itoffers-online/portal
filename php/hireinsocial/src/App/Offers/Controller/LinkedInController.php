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

use HireInSocial\Offers\Application\Command\User\LinkedInConnect;
use HireInSocial\Offers\Offers;
use League\OAuth2\Client\Provider\LinkedIn;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class LinkedInController extends AbstractController
{
    use LinkedInAccess;
    use RedirectAfterLogin;

    /**
     * @var Offers
     */
    private $offers;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var LinkedIn
     */
    private $linkedIn;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Offers $offers,
        RouterInterface $router,
        LinkedIn $linkedIn,
        LoggerInterface $logger
    ) {
        $this->offers = $offers;
        $this->logger = $logger;
        $this->router = $router;
        $this->linkedIn = $linkedIn;
    }

    public function loginAction(Request $request) : Response
    {
        if ($request->getSession()->has(SecurityController::USER_SESSION_KEY)) {
            return $this->redirectToRoute('home');
        }

        return $this->render('@offers/linkedin/login.html.twig', [
            'linkedin_login_url' => $this->linkedIn->getAuthorizationUrl([
                'scope' => ['r_liteprofile','r_emailaddress'],
                'redirect_uri' => $this->generateUrl('linkedin_login_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]),
        ]);
    }

    public function loginSuccessAction(Request $request) : Response
    {
        if (!$request->query->has('code')) {
            $this->logger->debug('Linked In login success action does not have code', [$request->query->all()]);

            return $this->redirectToRoute('linkedin_login');
        }

        $token = $this->linkedIn->getAccessToken('authorization_code', [
            'code' => $request->query->get('code'),
            'redirect_uri' => $this->generateUrl('linkedin_login_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        $linkedInUser = $this->getLinkedInUser($this->linkedIn, $token);

        if ($user = $this->offers->userQuery()->findByEmail($linkedInUser['email'])) {
            if ($user->linkedInAppId() !== $linkedInUser['id']) {
                $this->addFlash('warning', $this->renderView('@offers/alert/linkedin_email_already_used.txt'));

                return $this->redirectToRoute('linkedin_login');
            }
        }

        $this->offers->handle(new LinkedInConnect($linkedInUser['id'], $linkedInUser['email']));

        $user = $this->offers->userQuery()->findByLinkedIn($linkedInUser['id']);

        if ($user->isBlocked()) {
            return $this->redirectToRoute('user_blocked');
        }

        $request->getSession()->set(SecurityController::USER_SESSION_KEY, $user->id());

        if ($this->hasRedirection($request->getSession())) {
            return $this->generateRedirection($request->getSession(), $this->router);
        }

        return $this->redirectToRoute('home');
    }

    public function logoutAction(Request $request) : Response
    {
        return $this->redirectToRoute('home');
    }
}
