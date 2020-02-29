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

namespace ITOffers\Offers\Application\Facebook;

use ITOffers\Offers\Application\Exception\Exception;
use ITOffers\Offers\Application\Specialization\Specialization;

final class FacebookGroupService
{
    private Facebook $facebook;

    public function __construct(Facebook $facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * Returns facebook post id
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
