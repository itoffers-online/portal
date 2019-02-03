<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Functional\Symfony;

use HireInSocial\Tests\Application\Functional\WebTestCase;

final class FacebookAuthenticationTest extends WebTestCase
{
    public function test_redirect_to_facebook_when_want_to_add_new_offer_not_logged()
    {
        $client = static::createClient();
        $client->request('GET', $client->getContainer()->get('router')->generate('offer_new', ['specialization' => 'php']));

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertEquals('/facebook/login', $client->getResponse()->headers->get('location'));
    }
}
