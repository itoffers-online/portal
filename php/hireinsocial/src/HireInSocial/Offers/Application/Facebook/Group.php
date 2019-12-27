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

namespace HireInSocial\Offers\Application\Facebook;

use HireInSocial\Offers\Application\Assertion;

final class Group
{
    /**
     * @var string
     */
    private $fbId;

    public function __construct(string $fbId)
    {
        Assertion::betweenLength($fbId, 3, 255, 'Invalid FB Group ID');

        $this->fbId = $fbId;
    }

    public function fbId() : string
    {
        return $this->fbId;
    }
}
