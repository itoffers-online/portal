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

namespace ITOffers\Offers\Application\Query\Specialization\Model\Specialization;

final class TwitterChannel
{
    /**
     * @var string
     */
    private $accountId;

    /**
     * @var string
     */
    private $screenName;

    public function __construct(string $accountId, string $screenName)
    {
        $this->accountId = $accountId;
        $this->screenName = \mb_strtolower($screenName);
    }

    /**
     * @return string
     */
    public function accountId() : string
    {
        return $this->accountId;
    }

    /**
     * @return string
     */
    public function screenName() : string
    {
        return $this->screenName;
    }
}
