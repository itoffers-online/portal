<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

trait RedirectAfterLogin
{
    public function redirectAfterLogin(SessionInterface $session, string $routeName, array $parameters = []) : void
    {
        $session->set('_redirect_to', ['route' => $routeName, 'parameters' => $parameters]);
    }

    public function hasRedirection(SessionInterface $session) : bool
    {
        return $session->has('_redirect_to');
    }

    public function generateRedirection(SessionInterface $session, RouterInterface $router) : RedirectResponse
    {
        if (!$this->hasRedirection($session)) {
            throw new NotFoundHttpException('Redirection not found');
        }

        $redirection = $session->get('_redirect_to');

        $session->remove('_redirect_to');

        return new RedirectResponse($router->generate($redirection['route'], $redirection['parameters']));
    }
}
