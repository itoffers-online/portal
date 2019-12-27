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

use HireInSocial\Offers\Application\Exception\Exception;
use HireInSocial\Offers\Application\Specialization\Specialization;

final class FacebookGroupService
{
    /**
     * @var \HireInSocial\Offers\Application\Facebook\Facebook
     */
    private $facebook;

    public function __construct(Facebook $facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * @throws Exception
     */
    public function pagePostAtGroup(Draft $draft, Specialization $specialization) : string
    {
        if (!$specialization->facebookChannel()) {
            throw new Exception(sprintf('Specialization "%s" does not have facebook channel assigned.', $specialization->slug()));
        }

        return $this->facebook->postToGroupAsPage(
            $draft,
            $specialization->facebookChannel()->group(),
            $specialization->facebookChannel()->page()
        );
    }
}
