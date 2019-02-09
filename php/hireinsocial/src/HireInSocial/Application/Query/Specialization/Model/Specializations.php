<?php

declare(strict_types=1);

namespace HireInSocial\Application\Query\Specialization\Model;

final class Specializations extends \ArrayObject
{
    public function __construct(Specialization ...$offers)
    {
        parent::__construct($offers);
    }

    public function has(string $slug) : bool
    {
        return (bool) \array_filter(
            (array) $this,
            function (Specialization $specialization) use ($slug) {
                return $specialization->is($slug);
            }
        );
    }

    public function get(string $slug) : Specialization
    {
        return \current(
            \array_filter(
                (array) $this,
                function (Specialization $specialization) use ($slug) {
                    return $specialization->is($slug);
                }
            )
        );
    }
}
