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

namespace ITOffers\Offers\Infrastructure\Doctrine\DBAL\Platform;

use Doctrine\DBAL\Platforms\Keywords\PostgreSQL94Keywords;

final class PostgreSQL11Keywords extends PostgreSQL94Keywords
{
    /**
     * {@inheritdoc}
     */
    public function getName() : string
    {
        return 'PostgreSQL11';
    }
}
