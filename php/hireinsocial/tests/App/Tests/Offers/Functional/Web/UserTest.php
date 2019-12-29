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

namespace App\Tests\Offers\Functional\Web;

use App\Tests\Functional\Web\WebTestCase;

class UserTest extends WebTestCase
{
    public function test_user_profile_page() : void
    {
        $user = $this->systemContext->createUser();

        $client = static::createClient();
        $this->authenticate($client, $user);

        $crawler = $client->request(
            'GET',
            $client->getContainer()->get('router')->generate('user_profile')
        );

        $this->assertEquals($user->email(), $crawler->filter('span[data-user-email]')->text());
    }
}
