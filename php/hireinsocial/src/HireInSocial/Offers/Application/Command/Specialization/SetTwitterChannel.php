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

namespace HireInSocial\Offers\Application\Command\Specialization;

use HireInSocial\Offers\Application\Command\ClassCommand;
use HireInSocial\Offers\Application\System\Command;

final class SetTwitterChannel implements Command
{
    use ClassCommand;

    /**
     * @var string
     */
    private $specSlug;

    /**
     * @var string
     */
    private $accountId;

    /**
     * @var string
     */
    private $screenName;

    /**
     * @var string
     */
    private $oauthToken;

    /**
     * @var string
     */
    private $oauthSecret;

    public function __construct(
        string $specSlug,
        string $accountId,
        string $screenName,
        string $oauthToken,
        string $oauthSecret
    ) {
        $this->specSlug = $specSlug;
        $this->accountId = $accountId;
        $this->screenName = $screenName;
        $this->oauthToken = $oauthToken;
        $this->oauthSecret = $oauthSecret;
    }

    public function specSlug() : string
    {
        return $this->specSlug;
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

    /**
     * @return string
     */
    public function oauthToken() : string
    {
        return $this->oauthToken;
    }

    /**
     * @return string
     */
    public function oauthSecret() : string
    {
        return $this->oauthSecret;
    }
}
