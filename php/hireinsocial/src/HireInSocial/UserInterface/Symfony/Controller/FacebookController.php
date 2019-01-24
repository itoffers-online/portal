<?php

declare(strict_types=1);

namespace HireInSocial\UserInterface\Symfony\Controller;

use Facebook\Facebook;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class FacebookController extends AbstractController
{
    public const FACEBOOK_ID_SESSION_KEY = 'his_user_fb_id';

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

    public function loginAction(Request $request) : Response
    {
        if ($request->getSession()->has(self::FACEBOOK_ID_SESSION_KEY)) {
            return $this->redirectToRoute('home');
        }

        $this->logger->debug('test');

        return $this->render('facebook/login.html.twig', [
            'facebook_login_url' => $this->facebook->getRedirectLoginHelper()->getLoginUrl(
                $this->generateUrl('facebook_login_success', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ),
        ]);
    }

    public function loginSuccessAction(Request $request) : Response
    {
        if (!$request->query->has('code')) {
            $this->logger->debug('Facebook login success action does not have code', [$request->query->all()]);

            return $this->redirectToRoute('facebook_login');
        }

        $this->logger->debug('Facebook login success action code exists.', ['code' => $request->query->get('code')]);

        $accessToken = $this->facebook->getOAuth2Client()->getAccessTokenFromCode(
            $request->query->get('code'),
            $this->generateUrl('facebook_login_success', [], UrlGeneratorInterface::ABSOLUTE_URL)
        );

        $facebookResponse = $this->facebook->get('me', $accessToken);
        $this->logger->debug('Facebook /me response', ['body' => $facebookResponse->getBody()]);

        $request->getSession()->set(self::FACEBOOK_ID_SESSION_KEY, $facebookResponse->getDecodedBody()['id']);

        return $this->redirectToRoute('home');
    }

    public function logoutAction(Request $request) : Response
    {
        if ($request->getSession()->has(self::FACEBOOK_ID_SESSION_KEY)) {
            $request->getSession()->remove(self::FACEBOOK_ID_SESSION_KEY);
        }

        return $this->redirectToRoute('home');
    }
}
