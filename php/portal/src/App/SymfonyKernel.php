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

namespace App;

use App\Notifications\Controller\EmailController;
use App\Offers\Controller\FacebookController;
use App\Offers\Controller\IndexController;
use App\Offers\Controller\LayoutController;
use App\Offers\Controller\LinkedInController;
use App\Offers\Controller\OfferController;
use App\Offers\Controller\ReCaptchaController;
use App\Offers\Controller\SecurityController;
use App\Offers\Controller\SpecializationController;
use App\Offers\Controller\StaticController;
use App\Offers\Controller\UserController;
use App\Offers\Routing\Factory;
use App\Offers\Twig\Extension\TwigFacebookExtension;
use App\Offers\Twig\Extension\TwigOfferExtension;
use App\Offers\Twig\Extension\TwigSpecializationExtension;
use Facebook\Facebook;
use ITOffers\Config;
use ITOffers\ITOffersOnline;
use ITOffers\Offers\Infrastructure\Imagine\UserInterface\ImagineOfferThumbnail;
use ITOffers\Offers\Infrastructure\Imagine\UserInterface\ImagineSpecializationThumbnail;
use ITOffers\Offers\Offers;
use ITOffers\Offers\UserInterface\OfferExtension;
use ITOffers\Offers\UserInterface\OfferThumbnail;
use ITOffers\Offers\UserInterface\SpecializationExtension;
use ITOffers\Offers\UserInterface\SpecializationThumbnail;
use League\OAuth2\Client\Provider\LinkedIn;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Twig\Extensions\DateExtension;
use Twig\Extensions\IntlExtension;
use Twig\Extensions\TextExtension;

final class SymfonyKernel extends Kernel
{
    private Config $config;

    use MicroKernelTrait;

    public function __construct(Config $config)
    {
        $this->config = $config;
        parent::__construct(
            $this->config->getString(Config::ENV),
            $this->config->getString(Config::ENV) !== 'prod'
        );
    }

    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new MonologBundle(),
            new SwiftmailerBundle(),
        ];
    }

    protected function initializeContainer() : void
    {
        parent::initializeContainer();

        $this->container->set(Config::class, $this->config);
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader) : void
    {
        $this->setupServices($c);
        $this->setupParameters($c);

        $this->setupFrameworkBundle($c);
        $this->setupTwigBundle($c);
        $this->setupMonologBundle($c);
        $this->setupSwiftMailerBundle($c);

        $c->autowire(IndexController::class)->addTag('controller.service_arguments');
        $c->autowire(FacebookController::class)->addTag('controller.service_arguments');
        $c->autowire(LinkedInController::class)->addTag('controller.service_arguments');
        $c->autowire(OfferController::class)->addTag('controller.service_arguments');
        $c->autowire(LayoutController::class)->addTag('controller.service_arguments');
        $c->autowire(SpecializationController::class)->addTag('controller.service_arguments');
        $c->autowire(ReCaptchaController::class)->addTag('controller.service_arguments');
        $c->autowire(UserController::class)->addTag('controller.service_arguments');
        $c->autowire(StaticController::class)->addTag('controller.service_arguments');
        $c->autowire(SecurityController::class)->addTag('controller.service_arguments');
        $c->autowire(EmailController::class)->addTag('controller.service_arguments');
    }

    public function getProjectDir() : string
    {
        return $this->config->getString(Config::ROOT_PATH);
    }

    public function getCacheDir() : string
    {
        return $this->config->getString(Config::CACHE_PATH) . '/symfony/' . $this->environment;
    }

    public function getLogDir() : string
    {
        return $this->config->getString(Config::LOGS_PATH);
    }

    protected function setupParameters(ContainerBuilder $c) : void
    {
        $c->setParameter('google_recaptcha_secret', $this->config->getString(Config::RECAPTCHA_SECRET));
        $c->setParameter('apply_email_template', $this->config->getString(Config::APPLY_EMAIL_TEMPLATE));
        $c->setParameter('itof.old_offer_days', $this->config->getInt(Config::OFFER_LIFETIME_DAYS));
    }

    protected function setupServices(ContainerBuilder $c) : void
    {
        $c->register(Config::class)
            ->setSynthetic(true)
            ->setPublic(true);

        $c->register(ITOffersOnline::class)
            ->setPublic(true)
            ->setAutowired(true)
            ->addMethodCall('boot');

        $c->register(Offers::class)
            ->setPublic(true)
            ->setAutowired(true)
            ->setFactory([new Reference(ITOffersOnline::class), 'offers']);

        $c->register(OfferExtension::class)
            ->addArgument($this->config->getString(Config::LOCALE))
            ->addArgument(new Reference(Offers::class));
        $c->register(SpecializationExtension::class)
            ->addArgument($this->config->getString(Config::LOCALE));

        $c->register(OfferThumbnail::class, ImagineOfferThumbnail::class)
            ->addArgument($this->getProjectDir())
            ->addArgument(new Reference(OfferExtension::class));

        $c->register(SpecializationThumbnail::class, ImagineSpecializationThumbnail::class)
            ->addArgument($this->getProjectDir())
            ->addArgument(new Reference(SpecializationExtension::class));

        $c->autowire(Facebook::class)
            ->addArgument([
                'app_id' => $this->config->getString(Config::FB_APP_ID),
                'app_secret' => $this->config->getString(Config::FB_APP_SECRET),
            ]);

        $c->autowire(LinkedIn::class)
            ->addArgument([
                'clientId' => $this->config->getString(Config::LINKEDIN_APP_ID),
                'clientSecret' => $this->config->getString(Config::LINKEDIN_APP_SECRET),
            ]);
    }

    protected function configureRoutes(RouteCollectionBuilder $routes) : void
    {
        Factory::addRoutes($routes, $this->environment);
        Factory::addLocalizedRoutes($routes, $this->config->getString(Config::LOCALE));
    }

    protected function setupFrameworkBundle(ContainerBuilder $c) : void
    {
        $parameters = [
            'secret' => $this->config->getString(Config::SYMFONY_SECRET),
            'csrf_protection' => null,
            'esi' => [
                'enabled' => true,
            ],
            'validation' => [
                'enabled' => true,
                'enable_annotations' => false,
            ],
            'annotations' => [
                'enabled' => false,
            ],
            'session' => [
                'cookie_samesite' => 'strict',
                'save_path' => sys_get_temp_dir() . '/his/sessions',
            ],
            'default_locale' => $this->config->getString(Config::LOCALE),
            'translator' => [
                'fallbacks' => [$this->config->getString(Config::LOCALE)],
                'paths' => [
                    $this->config->getString(Config::ROOT_PATH) . '/resources/translations',
                ],
            ],
            'templating' => [
                'engines' => [
                    'twig',
                ],
            ],
        ];

        if ($this->config->getString(Config::ENV) === 'test') {
            $parameters['test'] = true;
            $parameters['session']['storage_id'] = 'session.storage.mock_file';
        }

        $c->loadFromExtension('framework', $parameters);
    }

    protected function setupTwigBundle(ContainerBuilder $c) : void
    {
        $c->loadFromExtension(
            'twig',
            [
                'paths' => [
                    $this->config->getString(Config::ROOT_PATH) . '/resources/templates/' . $this->config->getString(Config::LOCALE) . '/ui/theme' => 'theme',
                    $this->config->getString(Config::ROOT_PATH) . '/resources/templates/' . $this->config->getString(Config::LOCALE) . '/ui/offers' => 'offers',
                    $this->config->getString(Config::ROOT_PATH) . '/resources/templates/' . $this->config->getString(Config::LOCALE) . '/ui/notifications' => 'notifications',
                ],
                'default_path' => $this->config->getString(Config::ROOT_PATH) . '/resources/templates',
                'date' => [
                    'timezone' => $this->config->getString(Config::TIMEZONE),
                ],
                'cache' => $this->getCacheDir() . '/twig',
                'globals' => [
                    'apply_email_template' => $this->config->getString(Config::APPLY_EMAIL_TEMPLATE),
                    'facebook' => [
                        'app_id' => $this->config->getString(Config::FB_APP_ID),
                        'page_url' => $this->config->getString(Config::FB_PAGE_URL),
                    ],
                    'google' => [
                        'recaptcha' => [
                            'key' => $this->config->getString(Config::RECAPTCHA_KEY),
                        ],
                        'maps' => [
                            'key' => $this->config->getString(Config::GOOGLE_MAPS_KEY),
                        ],
                        'analytics' => [
                            'code' => $this->config->getString(Config::GOOGLE_ANALYTICS_CODE),
                        ],
                    ],
                    'assets' => [
                        'storage_url' => $this->config->getJson(Config::FILESYSTEM_CONFIG)['storage_url'],
                    ],
                    'contact_email' => $this->config->getString(Config::CONTACT_EMAIL),
                    'report_email' => $this->config->getString(Config::REPORT_EMAIL),
                    'itof' => [
                        'old_offer_days' => $this->config->getInt(Config::OFFER_LIFETIME_DAYS),
                        'domain' => $this->config->getString(Config::DOMAIN),
                    ],
                ],
                'auto_reload' => $this->config->getString(Config::ENV) !== 'prod',
                'debug' => $this->config->getString(Config::ENV) !== 'prod',
            ]
        );

        $c->register(IntlExtension::class)->addTag('twig.extension');
        $c->register(TextExtension::class)->addTag('twig.extension');
        $c->register(DateExtension::class)->addTag('twig.extension');
        $c->register(TwigFacebookExtension::class)->addTag('twig.extension');

        $c->register(TwigOfferExtension::class)
            ->addArgument(new Reference(OfferExtension::class))
            ->addTag('twig.extension');

        $c->register(TwigSpecializationExtension::class)
            ->addArgument(new Reference(SpecializationExtension::class))
            ->addTag('twig.extension');
    }

    protected function setupMonologBundle(ContainerBuilder $c) : ContainerBuilder
    {
        if ($this->getEnvironment() === 'prod') {
            return $c->loadFromExtension(
                'monolog',
                [
                    'handlers' => [
                        'main' => [
                            'type'         => 'fingers_crossed',
                            'action_level' => 'critical',
                            'handler'      => 'deduplicated',
                        ],
                        'grouped' => [
                            'type'    => 'group',
                            'members' => ['streamed', 'deduplicated'],
                        ],
                        'streamed'  => [
                            'type'  => 'stream',
                            'path'  => '%kernel.logs_dir%/%kernel.environment%_symfony.log',
                            'level' => 'debug',
                        ],
                        'deduplicated' => [
                            'type'    => 'deduplication',
                            'handler' => 'swift',
                        ],
                        'swift' => [
                            'type'         => 'swift_mailer',
                            'from_email'   => $this->config->getString(Config::REPORT_EMAIL),
                            'to_email'     => $this->config->getString(Config::REPORT_EMAIL),
                            'subject'      => 'An Error Occurred!',
                            'level'        => 'debug',
                            'formatter'    => 'monolog.formatter.html',
                            'content_type' => 'text/html',
                        ],
                    ],
                ]
            );
        }

        return $c->loadFromExtension(
            'monolog',
            [
                'handlers' => [
                    'file_log' => [
                        'type' => 'stream',
                        'path' => '%kernel.logs_dir%/%kernel.environment%_symfony.log',
                        'level' => 'ERROR',
                        'channels' => [
                            '!event', '!console', '!request', '!security',
                        ],
                    ],
                ],
            ],
        );
    }

    protected function setupSwiftMailerBundle(ContainerBuilder $c) : ContainerBuilder
    {
        $parameters = [
            'host' => $this->config->getJson(Config::MAILER_CONFIG)['host'],
            'port' => $this->config->getJson(Config::MAILER_CONFIG)['port'],
            'username' => $this->config->getJson(Config::MAILER_CONFIG)['username'],
            'password' => $this->config->getJson(Config::MAILER_CONFIG)['password'],
            'timeout' => 10,
            'transport' => 'smtp',
            'spool' => [
                'type' => 'file',
                'path' => $this->config->getString(Config::CACHE_PATH) . '/swiftmailer/' . $this->getEnvironment(),
            ],
            'disable_delivery' => false,
        ];

        if ($this->getEnvironment() === 'test') {
            $parameters['spool'] = [
                'type' => 'memory',
            ];
            $parameters['disable_delivery'] = true;
        }

        return $c->loadFromExtension(
            'swiftmailer',
            $parameters
        );
    }
}
