<?php

declare(strict_types=1);

/*
 * This file is part of the itoffers.online project.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ITOffers\Notifications\Infrastructure\Twig;

use ITOffers\Notifications\Application\Email\EmailFormatter;
use ITOffers\Notifications\Application\Offer\Offer;
use Pelago\Emogrifier\CssInliner;
use Twig\Environment;

final class TwigEmailFormatter implements EmailFormatter
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function offerPostedSubject(Offer $offer) : string
    {
        return $this->twig->render('@notifications/email/offer/posted_subject.txt.twig', ['offer' => $offer]);
    }

    public function offerPostedBody(Offer $offer) : string
    {
        return CssInliner::fromHtml(
            $this->twig->render('@notifications/email/offer/posted_body.html.twig', ['offer' => $offer])
        )
            ->inlineCss()
            ->renderBodyContent();
    }

    public function extraOffersAddedSubject() : string
    {
        return $this->twig->render('@notifications/email/user/extra_offers_added_subject.txt.twig');
    }

    public function extraOffersAddedBody(int $expiresInDays, int $amount) : string
    {
        return CssInliner::fromHtml(
            $this->twig->render('@notifications/email/user/extra_offers_added_body.html.twig', ['expiresInDays' => $expiresInDays, 'amount' => $amount])
        )
            ->inlineCss()
            ->renderBodyContent();
    }

    public function offerAutoRenewsAddedSubject() : string
    {
        return $this->twig->render('@notifications/email/user/offer_auto_renews_added_subject.txt.twig');
    }

    public function offerAutoRenewsAddedBody(int $expiresInDays, int $amount) : string
    {
        return CssInliner::fromHtml(
            $this->twig->render('@notifications/email/user/offer_auto_renews_added_body.html.twig', ['expiresInDays' => $expiresInDays, 'amount' => $amount])
        )
            ->inlineCss()
            ->renderBodyContent();
    }
}
