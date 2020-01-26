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

namespace HireInSocial\Offers\Application\Query\Features;

use HireInSocial\Offers\Application\Exception\Exception;
use HireInSocial\Offers\Application\FeatureToggle;
use HireInSocial\Offers\Application\System\Query;

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
