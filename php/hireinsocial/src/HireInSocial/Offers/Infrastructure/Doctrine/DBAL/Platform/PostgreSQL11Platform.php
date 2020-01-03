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

namespace HireInSocial\Offers\Infrastructure\Doctrine\DBAL\Platform;

use Doctrine\DBAL\Platforms\PostgreSQL100Platform;

final class PostgreSQL11Platform extends PostgreSQL100Platform
{
    /**
     * {@inheritDoc}
     */
    protected function getReservedKeywordsClass() : string
    {
        return PostgreSQL11Keywords::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getDateTimeTypeDeclarationSQL(array $fieldDeclaration) : string
    {
        return 'TIMESTAMP(6) WITHOUT TIME ZONE';
    }

    /**
     *
     * {@inheritDoc}
     */
    public function getDateTimeTzTypeDeclarationSQL(array $fieldDeclaration) : string
    {
        return 'TIMESTAMP(6) WITH TIME ZONE';
    }

    /**
     * {@inheritDoc}
     */
    public function getNowExpression() : string
    {
        return 'LOCALTIMESTAMP(6)';
    }

    /**
     * {@inheritDoc}
     */
    public function getTimeTypeDeclarationSQL(array $fieldDeclaration) : string
    {
        return 'TIME(6) WITHOUT TIME ZONE';
    }

    /**
     * {@inheritDoc}
     */
    public function getDateTimeFormatString() : string
    {
        return 'Y-m-d H:i:s.u';
    }

    /**
     * {@inheritDoc}
     */
    public function getDateTimeTzFormatString() : string
    {
        return 'Y-m-d H:i:s.uO';
    }

    /**
     * {@inheritDoc}
     */
    public function getTimeFormatString() : string
    {
        return 'H:i:s.u';
    }
}
