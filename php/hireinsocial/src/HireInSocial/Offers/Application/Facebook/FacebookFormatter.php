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

namespace HireInSocial\Offers\Application\Facebook;

use HireInSocial\Offers\Application\Offer\Offer;
use HireInSocial\Offers\Application\Offer\OfferFormatter;
use Twig\Environment;

final class FacebookFormatter implements OfferFormatter
{
    /**
     * @var \Twig\Environment
     */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function format(Offer $offer, string $slug) : string
    {
        return $this->twig->render('/offer/facebook/page/group/offer.txt.twig', [
            'offer' => $offer,
            'offerSlug' => $slug,
        ]);
    }
}
