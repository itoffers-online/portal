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

namespace ITOffers\Offers\Infrastructure\Doctrine\ORM\Application\Offer;

use Doctrine\ORM\EntityManager;
use ITOffers\Offers\Application\Offer\CompanyLogo;
use ITOffers\Offers\Application\Offer\CompanyLogos;
use Ramsey\Uuid\UuidInterface;

final class ORMCompanyLogos implements CompanyLogos
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(CompanyLogo $companyLogo) : void
    {
        $this->entityManager->persist($companyLogo);
    }

    public function removeFor(UuidInterface $offerId) : void
    {
        $companyLogo = $this->entityManager->getRepository(CompanyLogo::class)->findOneBy(['offerId' => $offerId->toString()]);

        if ($companyLogo) {
            $this->entityManager->remove($companyLogo);
        }
    }
}
