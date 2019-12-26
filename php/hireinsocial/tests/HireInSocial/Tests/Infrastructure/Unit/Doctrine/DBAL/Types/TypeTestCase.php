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

namespace HireInSocial\Tests\Infrastructure\Unit\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;

abstract class TypeTestCase extends TestCase
{
    /**
     * @var AbstractPlatform
     */
    protected $platform;

    /**
     * @var Type
     */
    protected $type;

    protected function setUp() : void
    {
        if (!Type::hasType($this->getTypeName())) {
            Type::addType($this->getTypeName(), $this->getTypeClass());
        }

        $this->platform = new MockPlatform();
        $this->type     = Type::getType($this->getTypename());
    }

    abstract protected function getTypeName() : string;

    abstract protected function getTypeClass() : string;

    /**
     * @param mixed $value
     * @dataProvider dataProvider
     */
    public function test_converting_to_database_value($value) : void
    {
        $dbValue = $this->type->convertToDatabaseValue($value, $this->platform);
        $this->assertEquals($value, $this->type->convertToPHPValue($dbValue, $this->platform));
    }
}
