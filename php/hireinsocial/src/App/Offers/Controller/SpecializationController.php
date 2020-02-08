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

use App\Offers\Form\Type\OfferFilterType;
use HireInSocial\HireInSocial;
use HireInSocial\Offers\Application\Query\Offer\OfferFilter;
use HireInSocial\Offers\UserInterface\OfferExtension;
use HireInSocial\Offers\UserInterface\SpecializationThumbnail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

final class SpecializationController extends AbstractController
{
    /**
     * @var HireInSocial
     */
    private $hireInSocial;

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @var SpecializationThumbnail
     */
    private $specializationThumbnail;

    public function __construct(
        HireInSocial $hireInSocial,
        ParameterBagInterface $parameterBag,
        SpecializationThumbnail $specializationThumbnail
    ) {
        $this->hireInSocial = $hireInSocial;
        $this->parameterBag = $parameterBag;
        $this->specializationThumbnail = $specializationThumbnail;
    }

    public function offersAction(Request $request, string $specSlug, string $seniorityLevel = null) : Response
    {
        $specialization = $this->hireInSocial->offers()->specializationQuery()->findBySlug($specSlug);

        if (!$specialization) {
            throw $this->createNotFoundException();
        }

        $seniorityLevel = $seniorityLevel
            ? OfferExtension::seniorityLevelFromName($seniorityLevel)
            : null;

        /** @var OfferFilter $offerFilter */
        $offerFilter = OfferFilter::allFor($specialization->slug(), $this->parameterBag->get('his.old_offer_days'))
            ->max(12);

        $form = $this->get('form.factory')->createNamed('offers', OfferFilterType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('remote')->getData()) {
                $offerFilter->onlyRemote();
            }

            if ($form->get('with_salary')->getData()) {
                $offerFilter->onlyWithSalary();
            }

            if ($sortBy = $form->get('sort_by')->getData()) {
                $offerFilter->sortBy($sortBy);
            }
        }

        $offersSeniorityLevels = $this->hireInSocial->offers()->offerQuery()->offersSeniorityLevels($offerFilter);

        if ($seniorityLevel) {
            $offerFilter->onlyFor($seniorityLevel);
        }

        $total = $this->hireInSocial->offers()->offerQuery()->count($offerFilter);

        if ($request->query->has('after')) {
            $offerFilter->showAfter($request->query->get('after'));
        }

        $offers = $this->hireInSocial->offers()->offerQuery()->findAll($offerFilter);
        $offerMore = $this->hireInSocial->offers()->offerQuery()->count($offerFilter);

        return $this->render('@offers/specialization/offers.html.twig', [
            'total' => $total,
            'offers' => $offers,
            'offersMore' => $offerMore,
            'showingOlder' => $request->query->has('after'),
            'specialization' => $specialization,
            'form' => $form->createView(),
            'queryParameters' => $request->query->all(),
            'throttleLimit' => $this->hireInSocial->offers()->offerThrottleQuery()->limit(),
            'throttleSince' => $this->hireInSocial->offers()->offerThrottleQuery()->since(),
            'offersSeniorityLevels' => $offersSeniorityLevels,
            'seniorityLevel' => $seniorityLevel,
        ]);
    }

    public function thumbnailAction(Request $request, string $specializationSlug) : Response
    {
        $specialization = $this->hireInSocial->offers()->specializationQuery()->findBySlug($specializationSlug);

        if (!$specialization) {
            throw $this->createNotFoundException();
        }

        $thumbnailPath = $this->specializationThumbnail->large($specialization, false);

        $response = new Response();
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, \basename($thumbnailPath));
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'image/png');
        $response->setContent(\file_get_contents($thumbnailPath));

        return $response;
    }
}
