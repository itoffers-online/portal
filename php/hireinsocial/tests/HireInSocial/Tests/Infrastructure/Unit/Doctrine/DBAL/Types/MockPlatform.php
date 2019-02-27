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
    }

    public function getIntegerTypeDeclarationSQL(array $columnDef)
    {
    }

    public function getBigIntTypeDeclarationSQL(array $columnDef)
    {
    }

    public function getSmallIntTypeDeclarationSQL(array $columnDef)
    {
    }

    public function _getCommonIntegerTypeDeclarationSQL(array $columnDef)
    {
    }

    public function getVarcharTypeDeclarationSQL(array $field)
    {
        return "DUMMYVARCHAR()";
    }

    /** @override */
    public function getClobTypeDeclarationSQL(array $field)
    {
        return 'DUMMYCLOB';
    }

    /**
     * {@inheritdoc}
     */
    public function getJsonTypeDeclarationSQL(array $field)
    {
        return 'DUMMYJSON';
    }

    /**
     * {@inheritdoc}
     */
    public function getBinaryTypeDeclarationSQL(array $field)
    {
        return 'DUMMYBINARY';
    }

    public function getVarcharDefaultLength()
    {
        return 255;
    }

    public function getName()
    {
        return 'mock';
    }

    protected function initializeDoctrineTypeMappings()
    {
    }

    protected function getVarcharTypeDeclarationSQLSnippet($length, $fixed)
    {
    }
}
