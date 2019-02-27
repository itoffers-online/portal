<?php

declare(strict_types=1);

namespace HireInSocial\UserInterface\Symfony;

use Facebook\Facebook;
use HireInSocial\Application\System;
use HireInSocial\UserInterface\Symfony\Controller\FacebookController;
use HireInSocial\UserInterface\Symfony\Controller\IndexController;
use HireInSocial\UserInterface\Symfony\Controller\LayoutController;
use HireInSocial\UserInterface\Symfony\Controller\OfferController;
use HireInSocial\UserInterface\Symfony\Controller\SpecializationController;
use HireInSocial\UserInterface\Twig\Extension\FacebookExtension;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Twig\Extensions\IntlExtension;
use Twig\Extensions\TextExtension;

final class SymfonyKernel extends Kernel
{
    private $projectRootPath;
    private $system;
    private $frameworkConfig;

    public function __construct(
        string $projectRootPath,
        string $environment,
        bool $debug,
        array $frameworkConfig,
        System $system
    ) {
        parent::__construct($environment, $debug);
        $this->projectRootPath = $projectRootPath;
        $this->frameworkConfig = $frameworkConfig;
        $this->system = $system;
    }

    use MicroKernelTrait;

    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new MonologBundle(),
        ];
    }

    protected function initializeContainer()
    {
        parent::initializeContainer();

        $this->container->set(System::class, $this->system);
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $c->loadFromExtension('framework', $this->frameworkConfig['framework']);
        $c->loadFromExtension('twig', $this->frameworkConfig['twig']);
        $c->loadFromExtension('monolog', $this->frameworkConfig['monolog']);

        $c->register(System::class)->setSynthetic(true);

        $c->autowire(Facebook::class)
            ->addArgument([
                'app_id' => $this->frameworkConfig['facebook']['app_id'],
                'app_secret' => $this->frameworkConfig['facebook']['app_secret'],
            ]);

        $c->register(FacebookExtension::class)->addTag('twig.extension');
        $c->register(IntlExtension::class)->addTag('twig.extension');
        $c->register(TextExtension::class)->addTag('twig.extension');

        $c->autowire(IndexController::class)->addTag('controller.service_arguments');
        $c->autowire(FacebookController::class)->addTag('controller.service_arguments');
        $c->autowire(OfferController::class)->addTag('controller.service_arguments');
        $c->autowire(LayoutController::class)->addTag('controller.service_arguments');
        $c->autowire(SpecializationController::class)->addTag('controller.service_arguments');
    }

    public function getProjectDir()
    {
        return $this->projectRootPath;
    }

    public function getCacheDir()
    {
        return $this->getProjectDir().'/var/cache/' . $this->environment . '/symfony';
    }

    public function getLogDir()
    {
        return $this->getProjectDir().'/var/logs';
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
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

        switch ($this->frameworkConfig['framework']['default_locale']) {
            case 'pl_PL':
                $routes->addRoute(new Route('/oferty/dodaj', ['_controller' => [OfferController::class, 'postAction']]), 'offer_post');
                $routes->addRoute(new Route('/oferty/{specSlug}/dodaj', ['_controller' => [OfferController::class, 'newAction']]), 'offer_new');
                $routes->addRoute(new Route('/oferty/{specSlug}/dodaj/sukces', ['_controller' => [OfferController::class, 'successAction']]), 'offer_success');
                $routes->addRoute(new Route('/oferty/{specSlug}', ['_controller' => [SpecializationController::class, 'offersAction']]), 'specialization_offers');
                $routes->addRoute(new Route('/oferta-pracy/{offerSlug}', ['_controller' => [OfferController::class, 'offerAction']]), 'offer');

                break;
            case 'en_US':
                $routes->addRoute(new Route('/offers/post', ['_controller' => [OfferController::class, 'postAction']]), 'offer_post');
                $routes->addRoute(new Route('/offers/{specSlug}/new', ['_controller' => [OfferController::class, 'newAction']]), 'offer_new');
                $routes->addRoute(new Route('/offers/{specSlug}/new/success', ['_controller' => [OfferController::class, 'successAction']]), 'offer_success');
                $routes->addRoute(new Route('/offers/{specSlug}', ['_controller' => [SpecializationController::class, 'offersAction']]), 'specialization_offers');
                $routes->addRoute(new Route('/job-offer/{slug}', ['_controller' => [OfferController::class, 'offerAction']]), 'offer');

                break;
            default:
                throw new \RuntimeException(sprintf('Unrecognized locale %s', $this->frameworkConfig['framework']['default_locale']));
        }
    }
}
