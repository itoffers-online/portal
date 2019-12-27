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

namespace HireInSocial\Offers\Application\Query\Offer\Model\Offer;

final class Contact
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string|null
     */
    private $phone;

    public function __construct(string $email, string $name, ?string $phone = null)
    {
        $this->email = $email;
        $this->name = $name;
        $this->phone = $phone;
    }

    public function email() : string
    {
        return $this->email;
    }

    public function name() : string
    {
        return $this->name;
    }

    public function phone() : ?string
    {
        return $this->phone;
    }
}
