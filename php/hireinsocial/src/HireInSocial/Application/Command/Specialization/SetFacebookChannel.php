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

namespace HireInSocial\Application\Command\Specialization;

use HireInSocial\Application\Command\ClassCommand;
use HireInSocial\Application\System\Command;

class SetFacebookChannel implements Command
{
    use ClassCommand;

    /**
     * @var string
     */
    private $specSlug;

    /**
     * @var string
     */
    private $facebookPageId;

    /**
     * @var string
     */
    private $facebookPageToken;

    /**
     * @var string
     */
    private $facebookGroupId;

    public function __construct(
        string $specSlug,
        string $facebookPageId,
        string $facebookPageToken,
        string $facebookGroupId
    ) {
        $this->specSlug = $specSlug;
        $this->facebookPageId = $facebookPageId;
        $this->facebookPageToken = $facebookPageToken;
        $this->facebookGroupId = $facebookGroupId;
    }

    public function specSlug() : string
    {
        return $this->specSlug;
    }

    public function facebookPageId() : string
    {
        return $this->facebookPageId;
    }

    public function facebookPageToken() : string
    {
        return $this->facebookPageToken;
    }

    public function facebookGroupId() : string
    {
        return $this->facebookGroupId;
    }
}
