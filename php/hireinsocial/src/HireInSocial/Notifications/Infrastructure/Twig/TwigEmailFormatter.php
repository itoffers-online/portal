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

namespace HireInSocial\Notifications\Infrastructure\Twig;

use HireInSocial\Notifications\Application\Email\EmailFormatter;
use HireInSocial\Notifications\Application\Offer\Offer;
use Pelago\Emogrifier\CssInliner;
use Twig\Environment;

final class TwigEmailFormatter implements EmailFormatter
{
    /**
     * @var Environment
     */
    private $twig;

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
}
