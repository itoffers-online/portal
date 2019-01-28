<?php

declare (strict_types=1);

namespace HireInSocial\Application\Specialization;

interface Specializations
{
    public function add(Specialization $specialization): void;
    public function get(string $slug) : Specialization;
}