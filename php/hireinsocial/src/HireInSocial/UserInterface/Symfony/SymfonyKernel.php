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
        $routes->add('/', [IndexController::class, 'homeAction'], 'home');
        $routes->add('/faq', [IndexController::class, 'faqAction'], 'faq');
        $routes->add('/facebook/login', [FacebookController::class, 'loginAction'], 'facebook_login');
        $routes->add('/facebook/logout', [FacebookController::class, 'logoutAction'], 'facebook_logout');
        $routes->add('/facebook/login/success', [FacebookController::class, 'loginSuccessAction'], 'facebook_login_success');
        $routes->add('/{specialization}/offer', [OfferController::class, 'newAction'], 'offer_new');
        $routes->add('/{specialization}/offer/success', [OfferController::class, 'successAction'], 'offer_success');
        $routes->add('/{slug}', [SpecializationController::class, 'offersAction'], 'specialization_offers');
    }
}
