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
use Faker\Factory;
use HireInSocial\Offers\Application\Query\Offer\OfferFilter;
use HireInSocial\Tests\Offers\Application\MotherObject\Command\Offer\PostOfferMother;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\HttpFoundation\Response;

final class OfferTest extends WebTestCase
{
    /**
     * @var string
     */
    private $specialization = 'php';

    public function setUp() : void
    {
        parent::setUp();

        $this->systemContext->createSpecialization($this->specialization);
    }

    public function test_new_offer_page() : void
    {
        $user = $this->systemContext->createUser();

        $client = static::createClient();
        $this->authenticate($client, $user);

        $crawler = $client->request(
            'GET',
            $client->getContainer()->get('router')->generate('offer_post')
        );

        $this->assertEquals(1, $crawler->filter('a[data-post-offer]')->count());
    }

    public function test_success_page_after_posting_offer() : void
    {
        $user = $this->systemContext->createUser();

        $client = static::createClient();
        $this->authenticate($client, $user);

        $crawler = $client->request(
            'GET',
            $client->getContainer()->get('router')->generate('offer_new', ['specSlug' => $this->specialization])
        );

        $faker = Factory::create();
        $form = $crawler->filter('form[name="offer"]')->form([
            'offer[company][name]' => 'Company name',
            'offer[company][url]' => 'http://company.com',
            'offer[company][description]' => $faker->text(512),
            'offer[position][name]' => 'Software Developer',
            'offer[position][description]' => $faker->text(1024),
            'offer[salary][min]' => 1000,
            'offer[salary][max]' => 5000,
            'offer[salary][currency]' => 'USD',
            'offer[salary][net]' => 1,
            'offer[contract]' => 'Contract (B2B)',
            'offer[location][remote]' => 1,
            'offer[location][name]' => 'Cracow',
            'offer[location][lat]' => '50.06212',
            'offer[location][lng]' => '19.9353153',
            'offer[description][requirements]' => $faker->text(1024),
            'offer[description][benefits]' => $faker->text(1024),
            'offer[contact][email]' => $faker->email,
            'offer[contact][name]' => $faker->name,
            'offer[contact][phone]' => '+12123123123',
            'offer[channels][facebook_group]' => 1,
            'offer[_token]' => $client->getContainer()->get('security.csrf.token_manager')->getToken('new_offer'),
        ]);

        /** @var ChoiceFormField $choiceField */
        $choiceField = $form['offer[channels][facebook_group]'];
        $choiceField->untick();

        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('.alert-success')->count());
    }

    public function test_offer_details_page() : void
    {
        $client = static::createClient();
        $user = $this->systemContext->createUser();
        $this->systemContext->offersFacade()->handle(PostOfferMother::random($user->id(), $this->specialization));

        $offer = $this->offersFacade()->offerQuery()->findAll(OfferFilter::allFor($this->specialization))->first();

        $client->request('GET', $client->getContainer()->get('router')->generate('offer', ['offerSlug' => $offer->slug()]));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function test_deleting_offer() : void
    {
        $user = $this->systemContext->createUser();
        $client = static::createClient();
        $this->authenticate($client, $user);

        $this->systemContext->offersFacade()->handle(PostOfferMother::random($user->id(), $this->specialization));

        $offer = $this->offersFacade()->offerQuery()->findAll(OfferFilter::allFor($this->specialization))->first();

        $client->request('GET', $client->getContainer()->get('router')->generate('offer', ['offerSlug' => $offer->slug()]));

        // go to confirmation page
        $client->click($client->getCrawler()->filter('[data-remove-offer]')->link());

        // confirm removing offer
        $client->click($client->getCrawler()->filter('[data-remove-offer]')->link());
        $client->followRedirect();

        $this->assertEquals(1, $client->getCrawler()->filter('[data-alert-success]')->count());
        $this->assertEquals(0, $this->offersFacade()->offerQuery()->total());
    }

    public function test_attempt_to_remove_offer_that_does_not_belong_to_the_user() : void
    {
        $user = $this->systemContext->createUser();
        $client = static::createClient();
        $this->systemContext->offersFacade()->handle(PostOfferMother::random($user->id(), $this->specialization));
        $offer = $this->offersFacade()->offerQuery()->findAll(OfferFilter::allFor($this->specialization))->first();

        $client->request('GET', $client->getContainer()->get('router')->generate('offer_remove', ['offerSlug' => $offer->slug()]));
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }
}
