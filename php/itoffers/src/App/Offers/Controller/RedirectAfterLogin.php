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
