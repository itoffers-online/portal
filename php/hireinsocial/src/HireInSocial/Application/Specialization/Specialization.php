<?php

declare(strict_types=1);

namespace HireInSocial\Application\Specialization;

use HireInSocial\Application\Assertion;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Specialization
{
    private $id;
    private $slug;
    private $facebookChannel;

    public function __construct(string $slug, FacebookChannel $facebookChannel)
    {
        Assertion::regex(\mb_strtolower($slug), '/^[a-z\-\_]+$/');
        Assertion::betweenLength($slug, 3, 255);

        $this->id = (string) Uuid::uuid4();
        $this->slug = \mb_strtolower($slug);
        $this->facebookChannel = $facebookChannel;
    }

    public function id(): UuidInterface
    {
        return Uuid::fromString($this->id);
    }

    public function is(string $slug) : bool
    {
        return $this->slug === \mb_strtolower($slug);
    }

    public function facebookChannel(): FacebookChannel
    {
        return $this->facebookChannel;
    }
}
