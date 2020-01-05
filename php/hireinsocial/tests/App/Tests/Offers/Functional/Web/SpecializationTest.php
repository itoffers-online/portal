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
use HireInSocial\Offers\Application\Query\Offer\OfferFilter;
use HireInSocial\Tests\Offers\Application\Double\Stub\CalendarStub;
use HireInSocial\Tests\Offers\Application\MotherObject\Command\Offer\PostOfferMother;
use Ramsey\Uuid\Uuid;

final class SpecializationTest extends WebTestCase
{
    /**
     * @var string
     */
    private $specialization = 'spec';

    public function setUp() : void
    {
        parent::setUp();

        $this->offersContext->createSpecialization($this->specialization);
    }

    public function test_specialization_offers_list() : void
    {
        $client = static::createClient();

        $this->offersContext->offersFacade()->handle(PostOfferMother::random(Uuid::uuid4()->toString(), $this->offersContext->createUser()->id(), $this->specialization));
        $this->offersContext->offersFacade()->handle(PostOfferMother::random(Uuid::uuid4()->toString(), $this->offersContext->createUser()->id(), $this->specialization));

        $crawler = $client->request(
            'GET',
            $client->getContainer()->get('router')->generate('specialization_offers', ['specSlug' => $this->specialization])
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertCount(2, $crawler->filter('[data-offer-id]'));
    }

    public function test_filter_out_offers_without_salary() : void
    {
        $this->offersFacade()->handle(PostOfferMother::withoutSalary(Uuid::uuid4()->toString(), $this->offersContext->createUser()->id(), $this->specialization));
        $this->offersFacade()->handle(PostOfferMother::withoutSalary(Uuid::uuid4()->toString(), $this->offersContext->createUser()->id(), $this->specialization));
        $this->offersFacade()->handle(PostOfferMother::random(Uuid::uuid4()->toString(), $this->offersContext->createUser()->id(), $this->specialization));

        $client = static::createClient();

        $crawler = $client->request(
            'GET',
            $client->getContainer()->get('router')->generate('specialization_offers', ['specSlug' => $this->specialization])
        );

        $form = $crawler
            ->filter("form[name=\"offers\"]")
            ->form(['offers[with_salary]' => true], 'GET');

        $crawler = $client->submit($form);

        $this->assertCount(1, $crawler->filter('[data-offer-id]'));
    }

    public function test_filter_out_not_remote_offers() : void
    {
        $this->offersFacade()->handle(PostOfferMother::notRemote(Uuid::uuid4()->toString(), $this->offersContext->createUser()->id(), $this->specialization));
        $this->offersFacade()->handle(PostOfferMother::notRemote(Uuid::uuid4()->toString(), $this->offersContext->createUser()->id(), $this->specialization));
        $this->offersFacade()->handle(PostOfferMother::remote(Uuid::uuid4()->toString(), $this->offersContext->createUser()->id(), $this->specialization));

        $client = static::createClient();

        $crawler = $client->request(
            'GET',
            $client->getContainer()->get('router')->generate('specialization_offers', ['specSlug' => $this->specialization])
        );

        $form = $crawler
            ->filter("form[name=\"offers\"]")
            ->form(['offers[remote]' => true], 'GET');

        $crawler = $client->submit($form);

        $this->assertCount(1, $crawler->filter('[data-offer-id]'));
    }

    public function test_sort_by_salary_ASC() : void
    {
        /** @var CalendarStub $calendar */
        $calendar = $this->offersFacade()->calendar();
        $calendar->goBack($seconds = 15);
        $this->offersFacade()->handle(PostOfferMother::withSalary(Uuid::uuid4()->toString(), $this->offersContext->createUser()->id(), $this->specialization, $min = 1000, $max = 5000));
        $calendar->goBack($seconds = 10);
        $this->offersFacade()->handle(PostOfferMother::withSalary(Uuid::uuid4()->toString(), $this->offersContext->createUser()->id(), $this->specialization, $min = 1000, $max = 3000));
        $calendar->goBack($seconds = 5);
        $this->offersFacade()->handle(PostOfferMother::withSalary(Uuid::uuid4()->toString(), $this->offersContext->createUser()->id(), $this->specialization, $min = 1000, $max = 7000));


        $client = static::createClient();

        $crawler = $client->request(
            'GET',
            $client->getContainer()->get('router')->generate('specialization_offers', ['specSlug' => $this->specialization])
        );

        $crawler = $client->submit(
            $crawler
                ->filter("form[name=\"offers\"]")
                ->form(['offers[sort_by]' => OfferFilter::SORT_SALARY_ASC], 'GET')
        );


        $salaries = \array_map(
            function (\DOMElement $node) {
                return (int) $node->getAttribute('data-salary-max');
            },
            (array) $crawler->filter('[data-salary-max]')->getIterator()
        );

        $this->assertEquals(3000, $salaries[0]);
        $this->assertEquals(5000, $salaries[1]);
        $this->assertEquals(7000, $salaries[2]);
    }

    public function test_sort_by_created_at_ASC() : void
    {
        /** @var CalendarStub $calendar */
        $calendar = $this->offersFacade()->calendar();
        $calendar->goBack($seconds = 15);
        $this->offersFacade()->handle(PostOfferMother::withSalary(Uuid::uuid4()->toString(), $this->offersContext->createUser()->id(), $this->specialization, $min = 1000, $max = 5000));
        $calendar->goBack($seconds = 10);
        $this->offersFacade()->handle(PostOfferMother::withSalary(Uuid::uuid4()->toString(), $this->offersContext->createUser()->id(), $this->specialization, $min = 1000, $max = 3000));
        $calendar->goBack($seconds = 5);
        $this->offersFacade()->handle(PostOfferMother::withSalary(Uuid::uuid4()->toString(), $this->offersContext->createUser()->id(), $this->specialization, $min = 1000, $max = 7000));


        $client = static::createClient();

        $crawler = $client->request(
            'GET',
            $client->getContainer()->get('router')->generate('specialization_offers', ['specSlug' => $this->specialization])
        );

        $crawler = $client->submit(
            $crawler
                ->filter("form[name=\"offers\"]")
                ->form(['offers[sort_by]' => OfferFilter::SORT_CREATED_AT_ASC], 'GET')
        );


        $salaries = \array_map(
            function (\DOMElement $node) {
                return (int) $node->getAttribute('data-salary-max');
            },
            (array) $crawler->filter('[data-salary-max]')->getIterator()
        );

        $this->assertEquals(7000, $salaries[0]);
        $this->assertEquals(3000, $salaries[1]);
        $this->assertEquals(5000, $salaries[2]);
    }
}
