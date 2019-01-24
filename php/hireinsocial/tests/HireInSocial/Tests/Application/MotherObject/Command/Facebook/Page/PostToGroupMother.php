<?php

declare(strict_types=1);

namespace HireInSocial\Tests\Application\MotherObject\Command\Facebook\Page;

use HireInSocial\Application\Command\Facebook\Page\PostToGroup;
use HireInSocial\Application\Command\Offer\Company;
use HireInSocial\Application\Command\Offer\Contact;
use HireInSocial\Application\Command\Offer\Contract;
use HireInSocial\Application\Command\Offer\Description;
use HireInSocial\Application\Command\Offer\Location;
use HireInSocial\Application\Command\Offer\Offer;
use HireInSocial\Application\Command\Offer\Position;
use HireInSocial\Application\Command\Offer\Salary;

final class PostToGroupMother
{
    public static function postAs(string $fbUserId) : PostToGroup
    {
        return new PostToGroup(
            $fbUserId,
            new Offer(
                new Company('Test sp. z o.o', 'https://test.com', 'Firma Test jest największa a zarazem najmniejsza firmą na świecie. Zatrudnia okolo 250 osób.'),
                new Position('PHP Developer', 'Osoba na tym stanowisku będzie zajmować się developmentem php'),
                new Location(true, 'Poland'),
                new Salary(1000, 5000, 'PLN', true),
                new Contract('B2B'),
                new Description(
                    'To są testowe wymagania na stanowisko w testowej firmie, dodane w celu sprawdzenia poprawności działania systemu.',
                    'To są testowe benefity do stanowiska w testowej firmie, dodane w celu sprawdzenia poprawności działania systemu.'
                ),
                new Contact(
                    'contact@test.com',
                    'Test HR Guy',
                    '+48999999999'
                )
            )
        );
    }
}
