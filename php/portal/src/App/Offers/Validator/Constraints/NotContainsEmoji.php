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

/**
 * @Annotation
 */
final class NotContainsEmoji extends Constraint
{
    public string $message = 'Text contains emoji, that at this point are not allowed.';

    public function validatedBy() : string
    {
        return NotContainsEmojiValidator::class;
    }
}
