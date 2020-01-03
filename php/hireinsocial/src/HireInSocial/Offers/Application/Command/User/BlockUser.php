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

namespace HireInSocial\Offers\Application\Command\User;

use HireInSocial\Offers\Application\Command\ClassCommand;
use HireInSocial\Offers\Application\System\Command;

final class BlockUser implements Command
{
    use ClassCommand;

    /**
     * @var string
     */
    private $userId;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getUserId() : string
    {
        return $this->userId;
    }
}
