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

namespace App\Offers\Controller;

use HireInSocial\Offers\Application\Query\Offer\OfferFilter;
use HireInSocial\Offers\Offers;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class IndexController extends AbstractController
{
    /**
     * @var Offers
     */
    private $offers;

    public function __construct(Offers $offers)
    {
        $this->offers = $offers;
    }

    public function homeAction(Request $request) : Response
    {
        /** @var OfferFilter $offerFilter */
        $offerFilter = OfferFilter::all()
            ->changeSize(50, 0);

        $offers = $this->offers->offerQuery()->findAll($offerFilter);

        return $this->render('@offers/home/index.html.twig', [
            'specializations' => $this->offers->specializationQuery()->all(),
            'offers' => $offers,
        ]);
    }

    public function faqAction(Request $request) : Response
    {
        return $this->render('@offers/home/faq.html.twig', []);
    }
}
