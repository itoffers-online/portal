<?php

declare(strict_types=1);

namespace HireInSocial\Application\Facebook;

use HireInSocial\Application\Offer\Offer;
use HireInSocial\Application\Offer\OfferFormatter;
use Twig_Environment;

final class FacebookFormatter implements OfferFormatter
{
    private $twig;

    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function format(Offer $offer): string
    {
        return $this->twig->render('/offer/facebook/page/group/offer.txt.twig', [
            'offer' => $offer,
        ]);
    }
}
