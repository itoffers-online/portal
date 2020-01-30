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

namespace App\Tests\Functional\Web;

use App\Offers\Controller\SecurityController;
use HireInSocial\Offers\Application\Query\User\Model\User;
use Symfony\Bundle\FrameworkBundle\Client;

abstract class WebTestCase extends SymfonyKernelTestCase
{
    /**
     * Creates a Client.
     *
     * @param array $options An array of options to pass to the createKernel method
     * @param array $server  An array of server parameters
     *
     * @return Client A Client instance
     */
    protected static function createClient(array $options = [], array $server = []) : Client
    {
        $kernel = static::bootKernel($options);

        $client = $kernel->getContainer()->get('test.client');
        $client->setServerParameters($server);

        return $client;
    }

    protected function authenticate(Client $client, User $user) : void
    {
        $client->getContainer()
            ->get('session')
            ->set(SecurityController::USER_SESSION_KEY, $user->id());
    }
}
