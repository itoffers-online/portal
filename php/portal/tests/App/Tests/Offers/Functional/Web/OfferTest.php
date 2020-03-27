<?php

declare(strict_types=1);

/*
 * This file is part of the itoffers.online project.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Offers\Functional\Web;

use App\Offers\Form\Type\Offer\ContactType;
use App\Tests\Functional\Web\WebTestCase;
use Faker\Factory;
use ITOffers\Config;
use ITOffers\Offers\Application\Query\Offer\Model\Offer\Salary;
use ITOffers\Offers\Application\Query\Offer\OfferFilter;
use ITOffers\Tests\Offers\Application\MotherObject\Command\Offer\PostOfferMother;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;

final class OfferTest extends WebTestCase
{
    private string $specialization = 'php';

    public function setUp() : void
    {
        parent::setUp();

        $this->offersContext->createSpecialization($this->specialization);
    }

    public function test_new_offer_page() : void
    {
        $user = $this->offersContext->createUser();

        $client = static::createClient();
        $this->authenticate($client, $user);

        $crawler = $client->request(
            'GET',
            $client->getContainer()->get('router')->generate('offer_post')
        );

        $this->assertEquals(1, $crawler->filter('a[data-post-offer]')->count());
    }

    public function test_success_page_after_posting_offer_as_a_recruiter() : void
    {
        $user = $this->offersContext->createUser();

        $client = static::createClient();
        $this->authenticate($client, $user);

        $crawler = $client->request(
            'GET',
            $client->getContainer()->get('router')->generate('offer_new', ['specSlug' => $this->specialization])
        );

        $faker = Factory::create();
        $form = $crawler->filter('form[name="offer"]')->form([
            'offer[locale]' => $locale = 'en_US',
            'offer[company][name]' => $companyName = 'Company name',
            'offer[company][url]' => $companyUrl = 'http://company.com',
            'offer[company][description]' => $companyDescription = $faker->text(512),
            'offer[position][seniorityLevel]' => $positionSeniorityLevel = \random_int(0, 4),
            'offer[position][name]' => $positionName = 'Software Developer',
            'offer[salary][min]' => $salaryMin = 1_000,
            'offer[salary][max]' => $salaryMax = 5_000,
            'offer[salary][currency]' => $salaryCurrency = 'USD',
            'offer[salary][net]' => $salaryIsNet = 1,
            'offer[salary][period_type]' => $salaryPeriodType = Salary::PERIOD_TYPE_MONTH,
            'offer[contract]' => $contract = 'Contract (B2B)',
            'offer[location][type]' => "1",
            'offer[location][address]' => $locationAddress = 'Kraków, Plac Szczepański 15',
            'offer[location][country]' => $locationCountry = 'PL',
            'offer[location][city]' => $locationCity = 'Cracow',
            'offer[location][lat]' => $locationLat = '50.06212',
            'offer[location][lng]' => $locationLng = '19.9353153',
            'offer[description][requirements][description]' => $descriptionRequirementsDescription = $faker->text(1_024),
            'offer[description][benefits]' => $descriptionRequirementsBenefits = $faker->text(1_024),
            'offer[description][technology_stack]' => $descriptionRequirementsTechStack = $faker->text(1_024),
            'offer[contact][type]' => ContactType::RECRUITER_TYPE,
            'offer[contact][email]' => $contactEmail = $faker->email,
            'offer[contact][name]' => $contactName = $faker->name,
            'offer[contact][phone]' => $contactPhone = '+12123123123',
            'offer[_token]' => $client->getContainer()->get('security.csrf.token_manager')->getToken('offer'),
        ]);

        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
        $this->assertEquals(1, $crawler->filter('.alert-success')->count());

        $this->assertSAme(
            1,
            $this->offersContext->module()->offerQuery()->count(OfferFilter::all())
        );

        $offer = $this->offersContext->module()->offerQuery()->findAll(OfferFilter::all())->first();

        $this->assertSAme(1, $this->offersContext->module()->offerQuery()->count(OfferFilter::all()));
        $this->assertSame($locale, $offer->locale());
        $this->assertSame($companyName, $offer->company()->name());
        $this->assertSame($companyUrl, $offer->company()->url());
        $this->assertSame($positionName, $offer->position()->name());
        $this->assertSame($positionSeniorityLevel, $offer->position()->seniorityLevel());
        $this->assertSame($salaryMin, $offer->salary()->min());
        $this->assertSame($salaryMax, $offer->salary()->max());
        $this->assertSame($salaryCurrency, $offer->salary()->currencyCode());
        $this->assertTrue($offer->salary()->isNet());
        $this->assertSame($salaryPeriodType, \mb_strtoupper($offer->salary()->periodType()));
        $this->assertFalse($offer->salary()->periodTypeTotal());
        $this->assertSame($locationAddress, $offer->location()->address());
        $this->assertSame($locationCity, $offer->location()->city());
        $this->assertSame($locationCountry, $offer->location()->countryCode());
        $this->assertSame((float) $locationLat, $offer->location()->lat());
        $this->assertSame((float) $locationLng, $offer->location()->lng());
        $this->assertSame($descriptionRequirementsDescription, $offer->description()->requirements()->description());
        $this->assertSame($descriptionRequirementsBenefits, $offer->description()->benefits());
        $this->assertSame($descriptionRequirementsTechStack, $offer->description()->technologyStack());
        $this->assertSame($contract, $offer->contract()->type());
        $this->assertTrue($offer->contact()->isRecruiter());
        $this->assertSame($contactEmail, $offer->contact()->email());
        $this->assertSame($contactName, $offer->contact()->name());
        $this->assertSame($contactPhone, $offer->contact()->phone());
    }

    public function test_success_page_after_posting_offer_from_external_source() : void
    {
        $user = $this->offersContext->createUser();

        $client = static::createClient();
        $this->authenticate($client, $user);

        $crawler = $client->request(
            'GET',
            $client->getContainer()->get('router')->generate('offer_new', ['specSlug' => $this->specialization])
        );

        $faker = Factory::create();
        $form = $crawler->filter('form[name="offer"]')->form([
            'offer[locale]' => $locale = 'en_US',
            'offer[company][name]' => $companyName = 'Company name',
            'offer[company][url]' => $companyUrl = 'http://company.com',
            'offer[company][description]' => $companyDescription = $faker->text(512),
            'offer[position][seniorityLevel]' => $positionSeniorityLevel = \random_int(0, 4),
            'offer[position][name]' => $positionName = 'Software Developer',
            'offer[salary][min]' => $salaryMin = 1_000,
            'offer[salary][max]' => $salaryMax = 5_000,
            'offer[salary][currency]' => $salaryCurrency = 'USD',
            'offer[salary][net]' => $salaryIsNet = 1,
            'offer[salary][period_type]' => $salaryPeriodType = Salary::PERIOD_TYPE_MONTH,
            'offer[contract]' => $contract = 'Contract (B2B)',
            'offer[location][type]' => "1",
            'offer[location][address]' => $locationAddress = 'Kraków, Plac Szczepański 15',
            'offer[location][country]' => $locationCountry = 'PL',
            'offer[location][city]' => $locationCity = 'Cracow',
            'offer[location][lat]' => $locationLat = '50.06212',
            'offer[location][lng]' => $locationLng = '19.9353153',
            'offer[description][requirements][description]' => $descriptionRequirementsDescription = $faker->text(1_024),
            'offer[description][benefits]' => $descriptionRequirementsBenefits = $faker->text(1_024),
            'offer[description][technology_stack]' => $descriptionRequirementsTechStack = $faker->text(1_024),
            'offer[contact][type]' => ContactType::EXTERNAL_SOURCE_TYPE,
            'offer[contact][url]' => $contactUrl = $faker->url,
            'offer[_token]' => $client->getContainer()->get('security.csrf.token_manager')->getToken('offer'),
        ]);

        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
        $this->assertEquals(1, $crawler->filter('.alert-success')->count());

        $offer = $this->offersContext->module()->offerQuery()->findAll(OfferFilter::all())->first();

        $this->assertSAme(1, $this->offersContext->module()->offerQuery()->count(OfferFilter::all()));
        $this->assertSame($locale, $offer->locale());
        $this->assertSame($companyName, $offer->company()->name());
        $this->assertSame($companyUrl, $offer->company()->url());
        $this->assertSame($positionName, $offer->position()->name());
        $this->assertSame($positionSeniorityLevel, $offer->position()->seniorityLevel());
        $this->assertSame($salaryMin, $offer->salary()->min());
        $this->assertSame($salaryMax, $offer->salary()->max());
        $this->assertSame($salaryCurrency, $offer->salary()->currencyCode());
        $this->assertTrue($offer->salary()->isNet());
        $this->assertSame($salaryPeriodType, \mb_strtoupper($offer->salary()->periodType()));
        $this->assertFalse($offer->salary()->periodTypeTotal());
        $this->assertSame($locationAddress, $offer->location()->address());
        $this->assertSame($locationCity, $offer->location()->city());
        $this->assertSame($locationCountry, $offer->location()->countryCode());
        $this->assertSame((float) $locationLat, $offer->location()->lat());
        $this->assertSame((float) $locationLng, $offer->location()->lng());
        $this->assertSame($descriptionRequirementsDescription, $offer->description()->requirements()->description());
        $this->assertSame($descriptionRequirementsBenefits, $offer->description()->benefits());
        $this->assertSame($descriptionRequirementsTechStack, $offer->description()->technologyStack());
        $this->assertSame($contract, $offer->contract()->type());
        $this->assertTrue($offer->contact()->isExternalSource());
        $this->assertSame($contactUrl, $offer->contact()->url());
    }

    public function test_success_page_after_editing_offer() : void
    {
        $client = static::createClient();
        $user = $this->offersContext->createUser();
        $this->authenticate($client, $user);
        $this->offersContext->module()->handle(PostOfferMother::random(Uuid::uuid4()->toString(), $user->id(), $this->specialization));

        $offer = $this->offersFacade()->offerQuery()->findAll(OfferFilter::allFor($this->specialization))->first();

        $crawler = $client->request(
            'GET',
            $client->getContainer()->get('router')->generate('offer_edit', ['specSlug' => $this->specialization, 'offer-slug' => $offer->slug()])
        );

        $faker = Factory::create();
        $form = $crawler->filter('form[name="offer"]')->form([
            'offer[locale]' => 'en_US',
            'offer[company][name]' => 'Updated Company Name',
            'offer[company][url]' => 'http://company.com',
            'offer[company][description]' => $faker->text(512),
            'offer[position][seniorityLevel]' => \random_int(0, 4),
            'offer[position][name]' => 'Software Developer',
            'offer[salary][min]' => 1_000,
            'offer[salary][max]' => 5_000,
            'offer[salary][currency]' => 'USD',
            'offer[salary][net]' => 1,
            'offer[salary][period_type]' => Salary::PERIOD_TYPE_MONTH,
            'offer[contract]' => 'Contract (B2B)',
            'offer[location][type]' => "1",
            'offer[location][address]' => 'Kraków, Plac Szczepański 15',
            'offer[location][country]' => 'PL',
            'offer[location][city]' => 'Cracow',
            'offer[location][lat]' => '50.06212',
            'offer[location][lng]' => '19.9353153',
            'offer[description][requirements][description]' => $faker->text(1_024),
            'offer[description][benefits]' => $faker->text(1_024),
            'offer[description][technology_stack]' => $faker->text(1_024),
            'offer[contact][email]' => $faker->email,
            'offer[contact][name]' => $faker->name,
            'offer[contact][phone]' => '+12123123123',
            'offer[_token]' => $client->getContainer()->get('security.csrf.token_manager')->getToken('offer'),
        ]);

        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
        $this->assertEquals(1, $crawler->filter('.alert-success')->count());

        $this->assertSAme(
            1,
            $this->offersContext->module()->offerQuery()->count(OfferFilter::all())
        );
        $this->assertSAme(
            'Updated Company Name',
            $this->offersContext->module()->offerQuery()->findBySlug($offer->slug())->company()->name()
        );
    }

    public function test_offer_details_page() : void
    {
        $client = static::createClient();
        $user = $this->offersContext->createUser();
        $this->offersContext->module()->handle(PostOfferMother::random(Uuid::uuid4()->toString(), $user->id(), $this->specialization));

        $offer = $this->offersFacade()->offerQuery()->findAll(OfferFilter::allFor($this->specialization))->first();

        $client->request('GET', $client->getContainer()->get('router')->generate('offer', ['offerSlug' => $offer->slug()]));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
    }

    public function test_deleting_offer() : void
    {
        $user = $this->offersContext->createUser();
        $client = static::createClient();
        $this->authenticate($client, $user);

        $this->offersContext->module()->handle(PostOfferMother::random(Uuid::uuid4()->toString(), $user->id(), $this->specialization));

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
        $user = $this->offersContext->createUser();
        $client = static::createClient();
        $this->offersContext->module()->handle(PostOfferMother::random(Uuid::uuid4()->toString(), $user->id(), $this->specialization));
        $offer = $this->offersFacade()->offerQuery()->findAll(OfferFilter::allFor($this->specialization))->first();

        $client->request('GET', $client->getContainer()->get('router')->generate('offer_remove', ['offerSlug' => $offer->slug()]));
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function test_posting_offer_again() : void
    {
        $user = $this->offersContext->createUser();

        $client = static::createClient();
        $this->authenticate($client, $user);


        $offer = $this->offersContext->createOffer($user->id(), $this->specialization);

        $crawler = $client->request(
            'GET',
            $client->getContainer()->get('router')->generate('offer_new', ['specSlug' => $this->specialization, 'offer-slug' => $offer->slug()])
        );

        $this->assertSame($offer->company()->name(), $crawler->filter('form[name="offer"]')->form()->get('offer[company][name]')->getValue());
        $this->assertSame($offer->company()->description(), $crawler->filter('form[name="offer"]')->form()->get('offer[company][description]')->getValue());
    }

    public function test_attempt_to_post_not_own_offer_again() : void
    {
        $authorOffer = $this->offersContext->createUser();
        $user = $this->offersContext->createUser();

        $client = static::createClient();
        $this->authenticate($client, $user);

        $offer = $this->offersContext->createOffer($authorOffer->id(), $this->specialization);

        $client->request(
            'GET',
            $client->getContainer()->get('router')->generate('offer_new', ['specSlug' => $this->specialization, 'offer-slug' => $offer->slug()])
        );

        $this->assertSame(403, $client->getResponse()->getStatusCode());
    }

    public function test_opening_expired_offer() : void
    {
        $client = static::createClient();

        $authorOffer = $this->offersContext->createUser();
        $user = $this->offersContext->createUser();

        $this->authenticate($client, $user);

        $this->setCurrentTime(new \DateTimeImmutable(\sprintf('-%d days', $this->config()->getInt(Config::OFFER_LIFETIME_DAYS) + 1)));

        $offer = $this->offersContext->createOffer($authorOffer->id(), $this->specialization);

        $crawler = $client->request('GET', $client->getContainer()->get('router')->generate('offer', ['offerSlug' => $offer->slug()]));

        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $this->assertSame("noindex", $crawler->filter('meta[name=robots]')->attr("content"));
        $this->assertCount(1, $crawler->filter('[data-offer-expired-warning]'));
    }

    public function test_assinging_offer_auto_renew() : void
    {
        $client = static::createClient();
        $user = $this->offersContext->createUser();
        $this->offersContext->addOfferAutRenewOffer($user, 10);
        $this->authenticate($client, $user);

        $this->offersContext->module()->handle(PostOfferMother::random(Uuid::uuid4()->toString(), $user->id(), $this->specialization));

        $offer = $this->offersFacade()->offerQuery()->findAll(OfferFilter::allFor($this->specialization))->first();

        $client->request('GET', $client->getContainer()->get('router')->generate('offer_assign_auto_renew', ['offerSlug' => $offer->slug()]));

        $client->followRedirect();

        $this->assertEquals(1, $client->getCrawler()->filter('[data-alert-success]')->count());
        $this->assertEquals(1, $this->offersFacade()->offerAutoRenewQuery()->countRenewsLeft($offer->id()->toString()));
    }
}
