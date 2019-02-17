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

        $user1 = $this->systemContext->createUser();
        $user2 = $this->systemContext->createUser();

        $this->systemContext->postToFacebookGroup($user1->id(), $specialization);
        $this->systemContext->postToFacebookGroup($user2->id(), $specialization);

        $crawler = $client->request('GET', $client->getContainer()->get('router')->generate('specialization_offers', ['specSlug' => $specialization]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertCount(2, $crawler->filter('.job-offer'));
    }
}
