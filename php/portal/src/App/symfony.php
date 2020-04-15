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

namespace App;

use ITOffers\Config;

function initializeSymfony(Config $config) : SymfonyKernel
{
    $kernel = new SymfonyKernel($config);
    $kernel->boot();

    return $kernel;
}
