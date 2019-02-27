<?php

declare(strict_types=1);

namespace App\Controller;

use Facebook\Facebook;
use HireInSocial\Application\Command\User\FacebookConnect;
use HireInSocial\Application\Query\User\UserQuery;
use HireInSocial\Application\System;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class FacebookController extends AbstractController
{
    use FacebookAccess;
    use RedirectAfterLogin;

    public const USER_SESSION_KEY = '_his_user_id';

    private $system;
    private $router;
    private $templating;
    private $facebook;
    private $logger;

    public function __construct(
        System $system,
        RouterInterface $router,
        EngineInterface $templating,
        Facebook $facebook,
        LoggerInterface $logger
    ) {
        $this->system = $system;
        $this->facebook = $facebook;
        $this->logger = $logger;
        $this->router = $router;
        $this->templating = $templating;
    }

    public function loginAction(Request $request) : Response
    {
        if ($request->getSession()->has(self::USER_SESSION_KEY)) {
            return $this->redirectToRoute('home');
        }

        return $this->templating->renderResponse('facebook/login.html.twig', [
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

        $fbUserAppId = $this->getUserId($this->facebook, $accessToken, $this->logger);
        $this->system->handle(new FacebookConnect($fbUserAppId));

        $user = $this->system->query(UserQuery::class)->findByFacebook($fbUserAppId);

        $request->getSession()->set(self::USER_SESSION_KEY, $user->id());

        if ($this->hasRedirection($request->getSession())) {
            return $this->generateRedirection($request->getSession(), $this->router);
        }

        return $this->redirectToRoute('home');
    }

    public function logoutAction(Request $request) : Response
    {
        if ($request->getSession()->has(self::USER_SESSION_KEY)) {
            $request->getSession()->remove(self::USER_SESSION_KEY);
        }

        return $this->redirectToRoute('home');
    }
}
