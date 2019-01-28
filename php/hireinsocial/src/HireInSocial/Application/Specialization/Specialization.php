<?php

declare (strict_types=1);

namespace HireInSocial\Application\Specialization;

use HireInSocial\Application\Assertion;

final class Specialization
{
    private $slug;
    private $name;
    private $facebookChannel;

    public function __construct(string $slug, string $name, FacebookChannel $facebookChannel)
    {
        Assertion::betweenLength($slug, 3, 255);
        Assertion::betweenLength($name, 3, 255);

        $this->slug = \mb_strtolower($slug);
        $this->name = $name;
        $this->facebookChannel = $facebookChannel;
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