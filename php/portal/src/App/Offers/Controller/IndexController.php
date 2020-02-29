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

namespace App\Offers\Controller;

use ITOffers\ITOffersOnline;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class IndexController extends AbstractController
{
    private ITOffersOnline $itoffers;

    public function __construct(ITOffersOnline $itoffers)
    {
        $this->itoffers = $itoffers;
    }

    public function homeAction(Request $request) : Response
    {
        return $this->render('@offers/home/index.html.twig', [
            'specializations' => $this->itoffers->offers()->specializationQuery()->all(),
        ]);
    }

    public function faqAction(Request $request) : Response
    {
        return $this->render('@offers/home/faq.html.twig', []);
    }
}
