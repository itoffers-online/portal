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

namespace ITOffers\Tests\Component\FeatureToggle\Unit;

use ITOffers\Component\CQRS\System\Command;
use ITOffers\Component\FeatureToggle\Feature;
use ITOffers\Component\FeatureToggle\FeatureToggle;
use ITOffers\Offers\Application\Exception\Exception;
use PHPUnit\Framework\TestCase;

final class FeatureToggleTest extends TestCase
{
    public function test_checking_if_command_is_disabled() : void
    {
        $toggle = new FeatureToggle(new class implements Feature {
            public function isEnabled() : bool
            {
                return false;
            }

            public function name() : string
            {
                return 'name';
            }

            public function isRelatedTo(Command $command) : bool
            {
                return true;
            }
        });

        $this->assertTrue($toggle->isDisabled(new class implements Command {
            public function commandName() : string
            {
                return 'fake command';
            }
        }));
    }

    public function test_checking_if_command_is_disabled_when_there_is_more_than_one_feature() : void
    {
        $toggle = new FeatureToggle(
            new class implements Feature {
                public function isEnabled() : bool
                {
                    return true;
                }

                public function name() : string
                {
                    return 'not_disabled';
                }

                public function isRelatedTo(Command $command) : bool
                {
                    return true;
                }
            },
            new class implements Feature {
                public function isEnabled() : bool
                {
                    return false;
                }

                public function name() : string
                {
                    return 'disabled';
                }

                public function isRelatedTo(Command $command) : bool
                {
                    return true;
                }
            }
        );

        $this->assertTrue($toggle->isDisabled(new class implements Command {
            public function commandName() : string
            {
                return 'fake command';
            }
        }));
    }

    public function test_try_to_get_not_existing_feature() : void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Feature with name "not_existing_feature", doesn\'t exist');

        (new FeatureToggle())->get('not_existing_feature');
    }
}
