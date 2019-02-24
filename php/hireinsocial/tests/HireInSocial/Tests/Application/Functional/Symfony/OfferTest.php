<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Functional\Symfony;

use HireInSocial\Application\Query\Offer\OfferFilter;
use HireInSocial\Application\Query\Offer\OfferQuery;
use HireInSocial\Tests\Application\Functional\WebTestCase;

final class OfferTest extends WebTestCase
{
    public function test_offer_success_page()
    {
        $specialization = 'spec';
        $client = static::createClient();
        $this->systemContext->createSpecialization($specialization);

        $client->request('GET', $client->getContainer()->get('router')->generate('offer_success', ['specSlug' => $specialization]));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function test_offer_details_page()
    {
        $specialization = 'spec';
        $client = static::createClient();
        $user = $this->systemContext->createUser();
        $this->systemContext->createSpecialization($specialization);
        $this->systemContext->postOffer($user->id(), $specialization);

        $offer = $this->system()->query(OfferQuery::class)->findAll(OfferFilter::allFor($specialization))->first();

        $client->request('GET', $client->getContainer()->get('router')->generate('offer', ['offerSlug' => $offer->slug()]));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
