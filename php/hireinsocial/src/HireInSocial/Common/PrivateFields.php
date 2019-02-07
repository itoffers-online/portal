<?php

declare (strict_types=1);

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