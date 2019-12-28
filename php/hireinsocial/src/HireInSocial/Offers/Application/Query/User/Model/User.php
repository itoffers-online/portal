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

namespace HireInSocial\Offers\Application\Query\User\Model;

final class User
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string|null
     */
    private $fbAppId;

    /**
     * @var bool
     */
    private $isBlocked;

    public function __construct(string $id, ?string $fbAppId, bool $isBlocked)
    {
        $this->id = $id;
        $this->fbAppId = $fbAppId;
        $this->isBlocked = $isBlocked;
    }

    public function id() : string
    {
        return $this->id;
    }

    public function fbAppId() : ?string
    {
        return $this->fbAppId;
    }

    /**
     * @return bool
     */
    public function isBlocked() : bool
    {
        return $this->isBlocked;
    }
}
