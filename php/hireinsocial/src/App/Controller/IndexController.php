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

namespace App\Controller;

use HireInSocial\Application\Query\Offer\OfferFilter;
use HireInSocial\Offers;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class IndexController extends AbstractController
{
    use ControllerTrait;

    private $offers;
    private $templating;

    public function __construct(Offers $offers, EngineInterface $templating)
    {
        $this->offers = $offers;
        $this->templating = $templating;
    }

    public function homeAction(Request $request) : Response
    {
        $offerFilter = OfferFilter::all()
            ->changeSize(50, 0);

        $offers = $this->offers->offerQuery()->findAll($offerFilter);

        return $this->templating->renderResponse('/home/index.html.twig', [
            'specializations' => $this->offers->specializationQuery()->all(),
            'offers' => $offers,
        ]);
    }

    public function faqAction(Request $request) : Response
    {
        return $this->templating->renderResponse('/home/faq.html.twig', []);
    }
}
