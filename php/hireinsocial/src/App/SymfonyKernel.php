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

namespace App;

use App\Offers\Controller\FacebookController;
use App\Offers\Controller\IndexController;
use App\Offers\Controller\LayoutController;
use App\Offers\Controller\OfferController;
use App\Offers\Controller\ReCaptchaController;
use App\Offers\Controller\SpecializationController;
use App\Offers\Controller\StaticController;
use App\Offers\Controller\UserController;
use App\Offers\Routing\Factory;
use App\Offers\Twig\Extension\FacebookExtension;
use Facebook\Facebook;
use HireInSocial\Offers\Offers;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Twig\Extensions\DateExtension;
use Twig\Extensions\IntlExtension;
use Twig\Extensions\TextExtension;

final class SymfonyKernel extends Kernel
{
    /**
     * @var string
     */
    private $projectRootPath;

    /**
     * @var Offers
     */
    private $offers;

    /**
     * @var mixed[]
     */
    private $frameworkConfig;

    public function __construct(
        string $projectRootPath,
        string $environment,
        bool $debug,
        array $frameworkConfig,
        Offers $offers
    ) {
        parent::__construct($environment, $debug);
        $this->projectRootPath = $projectRootPath;
        $this->frameworkConfig = $frameworkConfig;
        $this->offers = $offers;
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

    protected function initializeContainer() : void
    {
        parent::initializeContainer();

        $this->container->set(Offers::class, $this->offers);
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader) : void
    {
        $c->loadFromExtension('framework', $this->frameworkConfig['framework']);
        $c->loadFromExtension('twig', $this->frameworkConfig['twig']);
        $c->loadFromExtension('monolog', $this->frameworkConfig['monolog']);

        $c->register(Offers::class)->setSynthetic(true);

        $c->autowire(Facebook::class)
            ->addArgument([
                'app_id' => $this->frameworkConfig['facebook']['app_id'],
                'app_secret' => $this->frameworkConfig['facebook']['app_secret'],
            ]);

        foreach ($this->frameworkConfig['parameters'] as $key => $value) {
            $c->setParameter($key, $value);
        }

        $c->register(FacebookExtension::class)->addTag('twig.extension');
        $c->register(IntlExtension::class)->addTag('twig.extension');
        $c->register(TextExtension::class)->addTag('twig.extension');
        $c->register(DateExtension::class)->addTag('twig.extension');

        $c->autowire(IndexController::class)->addTag('controller.service_arguments');
        $c->autowire(FacebookController::class)->addTag('controller.service_arguments');
        $c->autowire(OfferController::class)->addTag('controller.service_arguments');
        $c->autowire(LayoutController::class)->addTag('controller.service_arguments');
        $c->autowire(SpecializationController::class)->addTag('controller.service_arguments');
        $c->autowire(ReCaptchaController::class)->addTag('controller.service_arguments');
        $c->autowire(UserController::class)->addTag('controller.service_arguments');
        $c->autowire(StaticController::class)->addTag('controller.service_arguments');
    }

    public function getProjectDir() : string
    {
        return $this->projectRootPath;
    }

    public function getCacheDir() : string
    {
        return $this->getProjectDir().'/var/cache/' . $this->environment . '/symfony';
    }

    public function getLogDir() : string
    {
        return $this->getProjectDir().'/var/logs';
    }

    protected function configureRoutes(RouteCollectionBuilder $routes) : void
    {
        Factory::addRoutes($routes);
        Factory::addLocalizedRoutes($routes, $this->frameworkConfig['framework']['default_locale']);
    }
}
