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

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class StaticController extends AbstractController
{
    public function termsAndConditionsAction() : Response
    {
        // generated with https://www.websitepolicies.com/
        return $this->render('@offers/static/terms_and_conditions.html.twig');
    }

    public function privacyPolicyAction() : Response
    {
        // generated with https://www.websitepolicies.com/
        return $this->render('@offers/static/privacy_policy.html.twig');
    }

    public function cookiesPolicyAction() : Response
    {
        // generated with https://www.websitepolicies.com/
        return $this->render('@offers/static/cookies_policy.html.twig');
    }
}
