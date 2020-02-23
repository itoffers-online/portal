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

namespace ITOffers\Offers\Infrastructure\Doctrine\DBAL\Types\Offer;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;
use ITOffers\Component\Reflection\PrivateFields;
use ITOffers\Offers\Application\Offer\Salary;

final class SalaryType extends JsonType
{
    use PrivateFields;

    public const NAME = 'itof_offer_salary';

    public function getName() : string
    {
        return self::NAME;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof Salary) {
            throw ConversionException::conversionFailedInvalidType($value, $this->getName(), [Salary::class]);
        }

        return \json_encode([
            'min' => self::getPrivatePropertyValue($value, 'min'),
            'max' => self::getPrivatePropertyValue($value, 'max'),
            'currency_code' => self::getPrivatePropertyValue($value, 'currencyCode'),
            'net' => self::getPrivatePropertyValue($value, 'net'),
            'period_type' => self::getPrivatePropertyValue(self::getPrivatePropertyValue($value, 'period'), 'type'),
        ]);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return $value;
        }

        $data = \json_decode($value, true);

        return new Salary(
            $data['min'],
            $data['max'],
            $data['currency_code'],
            $data['net'],
            Salary\Period::fromString($data['period_type'])
        );
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform) : bool
    {
        return true;
    }
}
