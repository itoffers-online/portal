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

namespace HireInSocial\Offers\Application\FeatureToggle;

use HireInSocial\Component\CQRS\System\Command;
use HireInSocial\Component\FeatureToggle\Feature;
use HireInSocial\Offers\Application\Command\Twitter\TweetAboutOffer;

final class TweetAboutOfferFeature implements Feature
{
    public const NAME = 'tweet_about_offer';

    /**
     * @var bool
     */
    private $enabled;

    public function __construct(bool $enabled)
    {
        $this->enabled = $enabled;
    }

    public function isEnabled() : bool
    {
        return $this->enabled;
    }

    public function name() : string
    {
        return self::NAME;
    }

    public function isRelatedTo(Command $command) : bool
    {
        return \get_class($command) === TweetAboutOffer::class;
    }
}
