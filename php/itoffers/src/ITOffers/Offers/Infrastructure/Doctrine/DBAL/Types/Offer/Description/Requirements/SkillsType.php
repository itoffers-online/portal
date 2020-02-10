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

namespace ITOffers\Offers\Infrastructure\Doctrine\DBAL\Types\Offer\Description\Requirements;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;
use ITOffers\Offers\Application\Offer\Description\Requirements\Skill;
use ITOffers\Offers\Common\PrivateFields;

final class SkillsType extends JsonType
{
    use PrivateFields;

    public const NAME = 'his_offer_description_requirements_skill';

    public function getName() : string
    {
        return self::NAME;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        if (!\is_array($value)) {
            throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['array']);
        }

        return \json_encode(
            \array_map(
                function (Skill $skill) {
                    return [
                        'name' => self::getPrivatePropertyValue($skill, 'name'),
                        'required' => self::getPrivatePropertyValue($skill, 'required'),
                        'experience_years' => self::getPrivatePropertyValue($skill, 'experienceYears'),
                    ];
                },
                $value
            )
        );
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        $data = \json_decode($value, true);

        if (!$data) {
            return [];
        }

        return \array_map(
            function (array $skillData) {
                return new Skill(
                    $skillData['name'],
                    (bool) $skillData['required'],
                    $skillData['experience_years'],
                );
            },
            $data
        );
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform) : bool
    {
        return true;
    }
}
