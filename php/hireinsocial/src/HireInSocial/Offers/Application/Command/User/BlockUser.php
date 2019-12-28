<?php


namespace HireInSocial\Offers\Application\Command\User;

use HireInSocial\Offers\Application\Command\ClassCommand;
use HireInSocial\Offers\Application\System\Command;

class BlockUser implements Command
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
    public function getUserId(): string
    {
        return $this->userId;
    }
}