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

namespace HireInSocial\Tests\Offers\Infrastructure\Unit\Doctrine\DBAL\Types;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Platforms\AbstractPlatform;

final class MockPlatform extends AbstractPlatform
{
    public function getBlobTypeDeclarationSQL(array $field)
    {
        throw DBALException::notSupported(__METHOD__);
    }

    public function getBooleanTypeDeclarationSQL(array $columnDef)
    {
        throw DBALException::notSupported(__METHOD__);
    }

    public function getIntegerTypeDeclarationSQL(array $columnDef)
    {
        throw DBALException::notSupported(__METHOD__);
    }

    public function getBigIntTypeDeclarationSQL(array $columnDef)
    {
        throw DBALException::notSupported(__METHOD__);
    }

    public function getSmallIntTypeDeclarationSQL(array $columnDef)
    {
        throw DBALException::notSupported(__METHOD__);
    }

    public function _getCommonIntegerTypeDeclarationSQL(array $columnDef)
    {
        throw DBALException::notSupported(__METHOD__);
    }

    public function getVarcharTypeDeclarationSQL(array $field)
    {
        return "DUMMYVARCHAR()";
    }

    /** @override */
    public function getClobTypeDeclarationSQL(array $field) : string
    {
        return 'DUMMYCLOB';
    }

    /**
     * {@inheritdoc}
     */
    public function getJsonTypeDeclarationSQL(array $field) : string
    {
        return 'DUMMYJSON';
    }

    /**
     * {@inheritdoc}
     */
    public function getBinaryTypeDeclarationSQL(array $field) : string
    {
        return 'DUMMYBINARY';
    }

    public function getVarcharDefaultLength() : int
    {
        return 255;
    }

    public function getName() : string
    {
        return 'mock';
    }

    protected function initializeDoctrineTypeMappings()
    {
        throw DBALException::notSupported(__METHOD__);
    }

    protected function getVarcharTypeDeclarationSQLSnippet($length, $fixed)
    {
        throw DBALException::notSupported(__METHOD__);
    }
}
