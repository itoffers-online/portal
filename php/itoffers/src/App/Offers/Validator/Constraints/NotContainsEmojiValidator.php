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

namespace App\Offers\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class NotContainsEmojiValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint) : void
    {
        if (!$constraint instanceof NotContainsEmoji) {
            throw new UnexpectedTypeException($constraint, NotContainsEmoji::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (\Emoji\detect_emoji($value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
