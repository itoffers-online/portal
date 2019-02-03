<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Functional\Symfony;

use HireInSocial\Tests\Application\Functional\WebTestCase;

final class OfferTest extends WebTestCase
{
    public function test_offer_success_page()
    {
        $slug = 'slug';
        $client = static::createClient();
        $this->systemContext->createSpecialization($slug);

        $client->request('GET', $client->getContainer()->get('router')->generate('offer_success', ['specialization' => $slug]));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
