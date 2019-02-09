<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Functional\Symfony;

use HireInSocial\Tests\Application\Functional\WebTestCase;

final class SpecializationTest extends WebTestCase
{
    public function test_specialization_offers_list()
    {
        $specialization = 'spec';
        $client = static::createClient();
        $this->systemContext->createSpecialization($specialization);

        $this->systemContext->postToFacebookGroup('FB_USER_ID_1', $specialization);
        $this->systemContext->postToFacebookGroup('FB_USER_ID_2', $specialization);

        $crawler = $client->request('GET', $client->getContainer()->get('router')->generate('specialization_offers', ['slug' => $specialization]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertCount(2, $crawler->filter('.job-offer'));
    }
}
