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

namespace ITOffers\Offers\Infrastructure;

use Aeon\Calendar\Gregorian\Calendar;
use Doctrine\ORM\EntityManager;
use Facebook\Facebook;
use ITOffers\Component\CQRS\EventStream;
use ITOffers\Component\CQRS\EventStream\Event;
use ITOffers\Component\CQRS\System;
use ITOffers\Component\CQRS\System\CommandBus;
use ITOffers\Component\CQRS\System\Queries;
use ITOffers\Component\EventBus\Infrastructure\InMemory\InMemoryEventBus;
use ITOffers\Component\FeatureToggle\FeatureToggle;
use ITOffers\Component\Mailer\Mailer;
use ITOffers\Config;
use ITOffers\Offers\Application\Command\Facebook\PagePostOfferAtGroupHandler;
use ITOffers\Offers\Application\Command\Offer\ApplyThroughEmailHandler;
use ITOffers\Offers\Application\Command\Offer\AssignAutoRenewHandler;
use ITOffers\Offers\Application\Command\Offer\PostOfferHandler;
use ITOffers\Offers\Application\Command\Offer\RemoveOfferHandler;
use ITOffers\Offers\Application\Command\Offer\RenewOfferHandler;
use ITOffers\Offers\Application\Command\Offer\UpdateOfferHandler;
use ITOffers\Offers\Application\Command\Specialization\CreateSpecializationHandler;
use ITOffers\Offers\Application\Command\Specialization\RemoveFacebookChannelHandler;
use ITOffers\Offers\Application\Command\Specialization\RemoveTwitterChannelHandler;
use ITOffers\Offers\Application\Command\Specialization\SetFacebookChannelHandler;
use ITOffers\Offers\Application\Command\Specialization\SetTwitterChannelHandler;
use ITOffers\Offers\Application\Command\Twitter\TweetAboutOfferHandler;
use ITOffers\Offers\Application\Command\User\AddExtraOffersHandler;
use ITOffers\Offers\Application\Command\User\AddOfferAutoRenewsHandler;
use ITOffers\Offers\Application\Command\User\BlockUserHandler;
use ITOffers\Offers\Application\Command\User\FacebookConnectHandler;
use ITOffers\Offers\Application\Command\User\LinkedInConnectHandler;
use ITOffers\Offers\Application\Exception\Exception;
use ITOffers\Offers\Application\Facebook\FacebookGroupService;
use ITOffers\Offers\Application\FeatureToggle\PostNewOffersFeature;
use ITOffers\Offers\Application\FeatureToggle\PostOfferAtFacebookGroupFeature;
use ITOffers\Offers\Application\FeatureToggle\TweetAboutOfferFeature;
use ITOffers\Offers\Application\Offer\EmailFormatter;
use ITOffers\Offers\Application\Offer\Event\OfferPostedEvent;
use ITOffers\Offers\Application\Offer\Throttling;
use ITOffers\Offers\Application\Query\Features\FeatureToggleQuery;
use ITOffers\Offers\Application\User\Event\ExtraOffersAdded;
use ITOffers\Offers\Application\User\Event\OfferAutoRenewsAdded;
use ITOffers\Offers\Infrastructure\Doctrine\DBAL\Application\Facebook\DbalFacebookFacebookQuery;
use ITOffers\Offers\Infrastructure\Doctrine\DBAL\Application\Offer\DbalApplicationQuery;
use ITOffers\Offers\Infrastructure\Doctrine\DBAL\Application\Offer\DbalOfferQuery;
use ITOffers\Offers\Infrastructure\Doctrine\DBAL\Application\Offer\DbalOfferThrottleQuery;
use ITOffers\Offers\Infrastructure\Doctrine\DBAL\Application\Specialization\DbalSpecializationQuery;
use ITOffers\Offers\Infrastructure\Doctrine\DBAL\Application\Twitter\DbalTweetsQuery;
use ITOffers\Offers\Infrastructure\Doctrine\DBAL\Application\User\DbalExtraOffersQuery;
use ITOffers\Offers\Infrastructure\Doctrine\DBAL\Application\User\DbalOfferAutoRenewQuery;
use ITOffers\Offers\Infrastructure\Doctrine\DBAL\Application\User\DbalUserQuery;
use ITOffers\Offers\Infrastructure\Doctrine\ORM\Application\Facebook\ORMPosts;
use ITOffers\Offers\Infrastructure\Doctrine\ORM\Application\Offer\ORMApplications;
use ITOffers\Offers\Infrastructure\Doctrine\ORM\Application\Offer\ORMCompanyLogos;
use ITOffers\Offers\Infrastructure\Doctrine\ORM\Application\Offer\ORMOfferPDFs;
use ITOffers\Offers\Infrastructure\Doctrine\ORM\Application\Offer\ORMOffers;
use ITOffers\Offers\Infrastructure\Doctrine\ORM\Application\Offer\ORMSlugs;
use ITOffers\Offers\Infrastructure\Doctrine\ORM\Application\Specialization\ORMSpecializations;
use ITOffers\Offers\Infrastructure\Doctrine\ORM\Application\System\ORMTransactionManager;
use ITOffers\Offers\Infrastructure\Doctrine\ORM\Application\Twitter\ORMTweets;
use ITOffers\Offers\Infrastructure\Doctrine\ORM\Application\User\ORMExtraOffers;
use ITOffers\Offers\Infrastructure\Doctrine\ORM\Application\User\ORMOfferAutoRenews;
use ITOffers\Offers\Infrastructure\Doctrine\ORM\Application\User\ORMUsers;
use ITOffers\Offers\Infrastructure\Facebook\FacebookGraphSDK;
use ITOffers\Offers\Infrastructure\Flysystem\Application\System\FlysystemStorage;
use ITOffers\Offers\Infrastructure\PHP\Hash\SHA256Encoder;
use ITOffers\Offers\Infrastructure\Twitter\OAuthTwitter;
use ITOffers\Offers\Offers;
use ITOffers\Tests\Offers\Application\Double\Dummy\DummyFacebook;
use ITOffers\Tests\Offers\Application\Double\Dummy\DummyTwitter;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Twig\Environment;

function offersFacade(
    Config $config,
    EntityManager $entityManager,
    Mailer $mailer,
    Environment $twig,
    Calendar $calendar,
    InMemoryEventBus $eventBus,
    LoggerInterface $logger
) : Offers {
    $dbalConnection = $entityManager->getConnection();
    $eventStream = new class($eventBus) implements EventStream {
        /**
         * @var Event[]
         */
        private array $events;

        private InMemoryEventBus $eventBus;

        public function __construct(InMemoryEventBus $eventBus)
        {
            $this->events = [];
            $this->eventBus = $eventBus;
        }

        public function record(Event $event) : void
        {
            $this->events[] = $event;
        }

        public function flush() : void
        {
            foreach ($this->events as $event) {
                switch (\get_class($event)) {
                    case OfferPostedEvent::class:
                        $name = InMemoryEventBus::OFFERS_EVENT_OFFER_POST;

                        break;
                    case ExtraOffersAdded::class:
                        $name = InMemoryEventBus::OFFERS_EVENT_USER_EXTRA_OFFERS_ADDED;

                        break;
                    case OfferAutoRenewsAdded::class:
                        $name = InMemoryEventBus::OFFERS_EVENT_USER_OFFER_AUTO_RENEW_ADDED;

                        break;
                    default:
                        throw new Exception(\sprintf("Unknown event class %s", \get_class($event)));
                }

                $this->eventBus->publishTo(InMemoryEventBus::TOPIC_OFFERS, new \ITOffers\Component\EventBus\Event(
                    $event->id(),
                    $event->occurredAt(),
                    $name,
                    $event->payload()
                ));
            }

            $this->events = [];
        }
    };

    switch ($config->getString(Config::ENV)) {
        case 'dev':
        case 'prod':
            $twitter = new OAuthTwitter(
                $config->getString(Config::TWITTER_API_KEY),
                $config->getString(Config::TWITTER_API_SECRET_KEY),
            );
            $facebook = new FacebookGraphSDK(
                new Facebook([
                    'app_id' => $config->getString(Config::FB_INTERNAL_APP_ID),
                    'app_secret' => $config->getString(Config::FB_INTERNAL_APP_SECRET),
                ]),
                $logger
            );

            break;
        case 'test':
            $twitter = new DummyTwitter();
            $facebook = new DummyFacebook();

            break;
        default:
            throw new RuntimeException(sprintf('Unknown environment %s', $config->getString(Config::ENV)));
    }

    $ormSpecializations = new ORMSpecializations($entityManager);

    $throttling = Throttling::createDefault($calendar);
    $ormOffers = new ORMOffers($entityManager);
    $ormUsers = new ORMUsers($entityManager);
    $ormExtraOffers = new ORMExtraOffers($entityManager);
    $ormApplications = new ORMApplications($entityManager);
    $ormOfferAutoRenews = new ORMOfferAutoRenews($entityManager, $calendar);

    $encoder = new SHA256Encoder();
    $emailFormatter = new EmailFormatter($twig);

    $featureToggle = new FeatureToggle(
        new PostNewOffersFeature($config->getBool(Config::FEATURE_POST_NEW_OFFERS)),
        new PostOfferAtFacebookGroupFeature($config->getBool(Config::FEATURE_POST_OFFER_AT_FACEBOOK)),
        new TweetAboutOfferFeature($config->getBool(Config::FEATURE_TWEET_ABOUT_OFFER))
    );

    return new Offers(
        new System(
            new CommandBus(
                new ORMTransactionManager($entityManager),
                new CreateSpecializationHandler(
                    $ormSpecializations
                ),
                new SetFacebookChannelHandler(
                    $ormSpecializations
                ),
                new SetTwitterChannelHandler(
                    $ormSpecializations
                ),
                new RemoveFacebookChannelHandler(
                    $ormSpecializations
                ),
                new RemoveTwitterChannelHandler(
                    $ormSpecializations
                ),
                new PostOfferHandler(
                    $calendar,
                    $ormOffers,
                    $ormExtraOffers,
                    $ormUsers,
                    $throttling,
                    $ormSpecializations,
                    $ormSlugs = new ORMSlugs($entityManager),
                    $ormOfferPDFs = new ORMOfferPDFs($entityManager),
                    $ormCompanyLogos = new ORMCompanyLogos($entityManager),
                    $fileStorage = FlysystemStorage::create($config->getJson(Config::FILESYSTEM_CONFIG)),
                    $eventStream
                ),
                new UpdateOfferHandler(
                    $calendar,
                    $ormOffers,
                    $ormSlugs,
                    $ormUsers,
                    $ormOfferPDFs,
                    $ormCompanyLogos,
                    $fileStorage
                ),
                new RemoveOfferHandler(
                    $ormUsers,
                    $ormOffers,
                    $calendar
                ),
                new PagePostOfferAtGroupHandler(
                    $ormOffers,
                    new ORMPosts($entityManager),
                    $ormSpecializations,
                    new FacebookGroupService($facebook)
                ),
                new TweetAboutOfferHandler(
                    $ormOffers,
                    new ORMTweets($entityManager),
                    $ormSpecializations,
                    $twitter
                ),
                new FacebookConnectHandler(
                    $ormUsers,
                    $calendar
                ),
                new LinkedInConnectHandler(
                    $ormUsers,
                    $calendar
                ),
                new BlockUserHandler(
                    $ormUsers,
                    $calendar
                ),
                new AddExtraOffersHandler(
                    $ormUsers,
                    $ormExtraOffers,
                    $eventStream,
                    $calendar
                ),
                new AddOfferAutoRenewsHandler(
                    $ormUsers,
                    $ormOfferAutoRenews,
                    $eventStream,
                    $calendar
                ),
                new AssignAutoRenewHandler(
                    $config->getInt(Config::OFFER_LIFETIME_DAYS),
                    $ormUsers,
                    $ormOffers,
                    $ormOfferAutoRenews,
                    $calendar
                ),
                new RenewOfferHandler(
                    $ormOffers,
                    $ormOfferAutoRenews,
                    $calendar
                ),
                new ApplyThroughEmailHandler(
                    $mailer,
                    $ormOffers,
                    $ormApplications,
                    $encoder,
                    $emailFormatter,
                    $calendar
                )
            ),
            new Queries(
                new DbalOfferThrottleQuery($throttling->limit(), $throttling->since(), $dbalConnection, $calendar),
                new DbalOfferQuery($dbalConnection, $calendar),
                new DbalSpecializationQuery($dbalConnection),
                new DbalUserQuery($dbalConnection),
                new DbalExtraOffersQuery($dbalConnection),
                new DbalOfferAutoRenewQuery($dbalConnection),
                new DbalApplicationQuery($dbalConnection, $encoder),
                new DbalFacebookFacebookQuery($dbalConnection),
                new DbalTweetsQuery($dbalConnection),
                new FeatureToggleQuery($featureToggle)
            ),
            $featureToggle,
            $calendar,
            $eventStream,
            $logger,
        ),
        $calendar
    );
}
