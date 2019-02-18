<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Double\Dummy;

use HireInSocial\Application\Offer\Offer;
use HireInSocial\Application\Offer\OfferFormatter;

final class DummyFormatter implements OfferFormatter
{
    public function format(Offer $offer): string
    {
        return 'This is dummy job offer';
    }
}
