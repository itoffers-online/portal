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

namespace App\Offers\Routing;

use App\Offers\Controller\FacebookController;
use App\Offers\Controller\IndexController;
use App\Offers\Controller\LinkedInController;
use App\Offers\Controller\OfferController;
use App\Offers\Controller\ReCaptchaController;
use App\Offers\Controller\SecurityController;
use App\Offers\Controller\SpecializationController;
use App\Offers\Controller\StaticController;
use App\Offers\Controller\UserController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollectionBuilder;

final class Factory
{
    public static function addRoutes(RouteCollectionBuilder $routes, string $env) : void
    {
        $routes->addRoute(
            new Route('/', ['_controller' => [IndexController::class, 'homeAction']]),
            'home'
        );
        $routes->addRoute(
            new Route('/faq', ['_controller' => [IndexController::class, 'faqAction']]),
            'faq'
        );
        $routes->addRoute(
            new Route('/offer/apply', ['_controller' => [OfferController::class, 'applyAction']]),
            'offer_apply'
        );
        $routes->addRoute(
            new Route('/offers/{offerSlug}/thumbnail', ['_controller' => [OfferController::class, 'thumbnailAction']]),
            'offer_thumbnail'
        );
        $routes->addRoute(
            new Route('/specialization/{specializationSlug}/thumbnail', ['_controller' => [SpecializationController::class, 'thumbnailAction']]),
            'specialization_thumbnail'
        );
        $routes->addRoute(
            new Route('/recaptcha/verify', ['_controller' => [ReCaptchaController::class, 'verifyAction']]),
            'recaptcha_verify'
        );
        $routes->addRoute(
            new Route('/login', ['_controller' => [SecurityController::class, 'loginAction']]),
            'login'
        );
        $routes->addRoute(
            new Route('/facebook/login', ['_controller' => [FacebookController::class, 'loginAction']]),
            'facebook_login'
        );
        $routes->addRoute(
            new Route('/facebook/logout', ['_controller' => [FacebookController::class, 'logoutAction']]),
            'facebook_logout'
        );
        $routes->addRoute(
            new Route('/facebook/login/success', ['_controller' => [FacebookController::class, 'loginSuccessAction']]),
            'facebook_login_success'
        );
        $routes->addRoute(
            new Route('/linkedin/login', ['_controller' => [LinkedInController::class, 'loginAction']]),
            'linkedin_login'
        );
        $routes->addRoute(
            new Route('/linkedin/logout', ['_controller' => [LinkedInController::class, 'logoutAction']]),
            'linkedin_logout'
        );
        $routes->addRoute(
            new Route('/linkedin/login/success', ['_controller' => [LinkedInController::class, 'loginSuccessAction']]),
            'linkedin_login_success'
        );


        if (\in_array($env, ['dev', 'test'], true)) {
            $routes->import('@FrameworkBundle/Resources/config/routing/errors.xml', '/_error');
        }
    }

    public static function addLocalizedRoutes(RouteCollectionBuilder $routes, string $locale) : void
    {
        switch ($locale) {
            case 'pl-PL':
                $routes->addRoute(new Route('/oferty/dodaj', ['_controller' => [OfferController::class, 'postAction']]), 'offer_post');
                $routes->addRoute(new Route('/oferty/{specSlug}/dodaj', ['_controller' => [OfferController::class, 'newAction']]), 'offer_new');
                $routes->addRoute(new Route('/oferty/{specSlug}/dodaj/sukces', ['_controller' => [OfferController::class, 'successAction']]), 'offer_success');
                $routes->addRoute(new Route('/oferty/{specSlug}', ['_controller' => [SpecializationController::class, 'offersAction']]), 'specialization_offers');
                $routes->addRoute(new Route('/oferta-pracy/{offerSlug}/potwierdz-usuniecie', ['_controller' => [OfferController::class, 'removeConfirmationAction']]), 'offer_remove_confirmation');
                $routes->addRoute(new Route('/oferta-pracy/{offerSlug}/usun', ['_controller' => [OfferController::class, 'removeAction']]), 'offer_remove');
                $routes->addRoute(new Route('/oferta-pracy/{offerSlug}', ['_controller' => [OfferController::class, 'offerAction']]), 'offer');

                break;
            case 'en-US':
                $routes->addRoute(new Route('/offers/post', ['_controller' => [OfferController::class, 'postAction']]), 'offer_post');
                $routes->addRoute(new Route('/offers/{specSlug}/new', ['_controller' => [OfferController::class, 'newAction']]), 'offer_new');
                $routes->addRoute(new Route('/offers/{specSlug}/new/success', ['_controller' => [OfferController::class, 'successAction']]), 'offer_success');
                $routes->addRoute(new Route('/offers/{specSlug}/{seniorityLevel}', ['_controller' => [SpecializationController::class, 'offersAction']]), 'specialization_offers_seniority');
                $routes->addRoute(new Route('/offers/{specSlug}', ['_controller' => [SpecializationController::class, 'offersAction']]), 'specialization_offers');
                $routes->addRoute(new Route('/job-offer/{offerSlug}/remove-confirmation', ['_controller' => [OfferController::class, 'removeConfirmationAction']]), 'offer_remove_confirmation');
                $routes->addRoute(new Route('/job-offer/{offerSlug}/remove', ['_controller' => [OfferController::class, 'removeAction']]), 'offer_remove');
                $routes->addRoute(new Route('/job-offer/{offerSlug}', ['_controller' => [OfferController::class, 'offerAction']]), 'offer');
                $routes->addRoute(new Route('/user/blocked', ['_controller' => [SecurityController::class, 'userBlockedAction']]), 'user_blocked');
                $routes->addRoute(new Route('/user/profile', ['_controller' => [UserController::class, 'profileAction']]), 'user_profile');
                $routes->addRoute(new Route('/terms-and-conditions', ['_controller' => [StaticController::class, 'termsAndConditionsAction']]), 'terms_and_conditions');
                $routes->addRoute(new Route('/privacy-policy', ['_controller' => [StaticController::class, 'privacyPolicyAction']]), 'privacy_policy');
                $routes->addRoute(new Route('/cookies-policy', ['_controller' => [StaticController::class, 'cookiesPolicyAction']]), 'cookies_policy');
                $routes->addRoute(new Route('/how-it-works', ['_controller' => [StaticController::class, 'howItWorksAction']]), 'how_it_works');

                break;
            default:
                throw new \RuntimeException(sprintf('Unrecognized locale %s', $locale));
        }
    }
}
