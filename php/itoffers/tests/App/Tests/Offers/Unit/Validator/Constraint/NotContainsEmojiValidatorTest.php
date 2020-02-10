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

namespace App\Tests\Offers\Unit\Validator\Constraint;

use App\Offers\Validator\Constraints\NotContainsEmoji;
use App\Offers\Validator\Constraints\NotContainsEmojiValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

final class NotContainsEmojiValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator() : ConstraintValidatorInterface
    {
        return new NotContainsEmojiValidator();
    }

    public function test_null_is_valid() : void
    {
        $this->validator->validate(null, new NotContainsEmoji());
        $this->assertNoViolation();
    }

    public function test_empty_string_is_valid() : void
    {
        $this->validator->validate('', new NotContainsEmoji());
        $this->assertNoViolation();
    }

    public function test_string_without_emoji_is_valid() : void
    {
        $this->validator->validate('Lorem ipsum', new NotContainsEmoji());
        $this->assertNoViolation();
    }

    public function test_string_with_emoji_is_invalid() : void
    {
        $this->validator->validate('Lorem ipsum 🤧', new NotContainsEmoji());

        $this->buildViolation('Text contains emoji, that at this point are not allowed.')
            ->assertRaised();
    }
}
