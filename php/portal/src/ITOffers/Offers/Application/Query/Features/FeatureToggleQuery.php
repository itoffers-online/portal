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

namespace ITOffers\Offers\Application\Query\Features;

use ITOffers\Component\CQRS\System\Query;
use ITOffers\Component\FeatureToggle\FeatureToggle;
use ITOffers\Offers\Application\Exception\Exception;

final class FeatureToggleQuery implements Query
{
    /**
     * @var FeatureToggle
     */
    private $featureToggle;

    public function __construct(FeatureToggle $featureToggle)
    {
        $this->featureToggle = $featureToggle;
    }

    public function isDisabled(string $featureName) : bool
    {
        try {
            return !$this->featureToggle->get($featureName)->isEnabled();
        } catch (Exception $e) {
            // what does not exist, can't be disabled
            return false;
        }
    }

    public function isEnabled(string $featureName) : bool
    {
        try {
            return $this->featureToggle->get($featureName)->isEnabled();
        } catch (Exception $e) {
            // what does not exist, can't be disabled
            return false;
        }
    }
}
