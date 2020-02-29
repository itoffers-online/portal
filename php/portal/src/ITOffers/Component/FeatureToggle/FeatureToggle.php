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

namespace ITOffers\Component\FeatureToggle;

use ITOffers\Component\CQRS\System\Command;
use ITOffers\Offers\Application\Exception\Exception;

final class FeatureToggle
{
    /**
     * @var Feature[]
     */
    private array

 $features;

    public function __construct(Feature ...$features)
    {
        $this->features = $features;
    }

    public function isDisabled(Command $command) : bool
    {
        foreach ($this->features as $feature) {
            if ($feature->isRelatedTo($command) && !$feature->isEnabled()) {
                return true;
            }
        }

        return false;
    }

    public function get(string $name) : Feature
    {
        foreach ($this->features as $feature) {
            if (\mb_strtolower($feature->name()) === \mb_strtolower($name)) {
                return $feature;
            }
        }

        throw new Exception(\sprintf("Feature with name \"%s\", doesn't exist", $name));
    }
}
