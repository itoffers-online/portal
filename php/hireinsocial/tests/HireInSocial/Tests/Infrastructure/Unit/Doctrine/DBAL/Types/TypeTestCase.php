<?php

declare(strict_types=1);

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

    protected function setUp()
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
     * @dataProvider dataProvider
     */
    public function test_converting_to_database_value($value) : void
    {
        $dbValue = $this->type->convertToDatabaseValue($value, $this->platform);
        self::assertEquals($value, $this->type->convertToPHPValue($dbValue, $this->platform));
    }
}
