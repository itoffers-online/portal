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

namespace HireInSocial\Offers\Infrastructure\Doctrine\ORM\Application\Offer;

use Doctrine\ORM\EntityManager;
use HireInSocial\Offers\Application\Offer\Application;
use HireInSocial\Offers\Application\Offer\Application\EmailHash;
use HireInSocial\Offers\Application\Offer\Applications;
use HireInSocial\Offers\Application\Offer\Offer;

final class ORMApplications implements Applications
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function alreadyApplied(EmailHash $emailHash, Offer $offer) : bool
    {
        return (bool) $this->entityManager->getRepository(Application::class)->findOneBy([
            'offerId' => $offer->id(),
            'emailHash' => $emailHash->toString(),
        ]);
    }

    public function add(Application $application) : void
    {
        $this->entityManager->persist($application);
    }
}
