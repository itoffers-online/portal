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
     * @var string
     */
    private $email;

    /**
     * @var string|null
     */
    private $fbAppId;

    /**
     * @var string|null
     */
    private $linkedInAppId;

    /**
     * @var bool
     */
    private $isBlocked;

    public function __construct(string $id, string $email, ?string $fbAppId, ?string $linkedInAppId, bool $isBlocked)
    {
        $this->id = $id;
        $this->email = $email;
        $this->fbAppId = $fbAppId;
        $this->linkedInAppId = $linkedInAppId;
        $this->isBlocked = $isBlocked;
    }

    public function id() : string
    {
        return $this->id;
    }

    public function email() : string
    {
        return $this->email;
    }

    public function fbAppId() : ?string
    {
        return $this->fbAppId;
    }

    public function linkedInAppId() : ?string
    {
        return $this->linkedInAppId;
    }

    public function isBlocked() : bool
    {
        return $this->isBlocked;
    }
}
