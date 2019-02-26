<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\Functional\Symfony;

use HireInSocial\Tests\Application\Functional\WebTestCase;

final class AuthenticationTest extends WebTestCase
{
    public function test_redirect_to_login_page_when_want_to_add_new_offer_not_logged()
    {
        $client = static::createClient();
        $client->request('GET', $client->getContainer()->get('router')->generate('offer_new', ['specSlug' => 'php']));

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertEquals($client->getContainer()->get('router')->generate('facebook_login'), $client->getResponse()->headers->get('location'));
    }
}
