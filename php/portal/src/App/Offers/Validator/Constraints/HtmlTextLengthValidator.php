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
use Symfony\Component\Validator\Constraints\LengthValidator;

final class HtmlTextLengthValidator extends LengthValidator
{
    public function validate($value, Constraint $constraint) : void
    {
        parent::validate(\strip_tags((string) $value), $constraint);
    }
}
