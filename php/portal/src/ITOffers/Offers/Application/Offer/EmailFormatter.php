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

namespace ITOffers\Offers\Application\Offer;

use Twig\Environment;

final class EmailFormatter
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function applicationSubject(string $originSubject) : string
    {
        return $this->twig->render('@offers/email/apply/offer.txt.twig', [
            'originSubject' => $originSubject,
        ]);
    }

    public function applicationBody(string $originMessage) : string
    {
        return $this->twig->render('@offers/email/apply/message.html.twig', [
            'originMessage' => $originMessage,
        ]);
    }
}
