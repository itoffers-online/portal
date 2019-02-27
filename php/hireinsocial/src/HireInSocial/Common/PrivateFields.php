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

namespace HireInSocial\Common;

use Doctrine\Instantiator\Instantiator;

trait PrivateFields
{
    private static function instantiate(string $class): object
    {
        $instantiator = new Instantiator();

        return $instantiator->instantiate($class);
    }

    /**
     * @throws \ReflectionException
     */
    private static function getPrivatePropertyValue(object $object, string $property)
    {
        $reflectionProperty = new \ReflectionProperty($object, $property);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($object);
    }

    /**
     * @throws \ReflectionException
     */
    private static function setPrivatePropertyValue(object $object, string $property, $value) : void
    {
        $reflectionProperty = new \ReflectionProperty($object, $property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }
}
