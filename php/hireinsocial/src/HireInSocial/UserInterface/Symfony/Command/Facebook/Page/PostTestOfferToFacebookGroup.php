<?php

declare(strict_types=1);

namespace HireInSocial\UserInterface\Symfony\Command\Facebook\Page;

use HireInSocial\Application\Command\Facebook\Page\PostToGroup;
use HireInSocial\Application\Command\Offer\Company;
use HireInSocial\Application\Command\Offer\Contact;
use HireInSocial\Application\Command\Offer\Contract;
use HireInSocial\Application\Command\Offer\Description;
use HireInSocial\Application\Command\Offer\Location;
use HireInSocial\Application\Command\Offer\Offer;
use HireInSocial\Application\Command\Offer\Position;
use HireInSocial\Application\Command\Offer\Salary;
use HireInSocial\Application\Query\Specialization\SpecializationQuery;
use HireInSocial\Application\System;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class PostTestOfferToFacebookGroup extends Command
{
    public const NAME = 'post:test:facebook:group:page';
    protected static $defaultName = self::NAME;

    private $system;

    public function __construct(System $system)
    {
        parent::__construct();

        $this->system = $system;
    }

    protected function configure() : void
    {
        $this
            ->setDescription('<info>[Facebook]</info> Post test job offer at Facebook group as a page.')
            ->addArgument('specialization', InputArgument::REQUIRED, 'Specialization slug where for which test offer should be posted.')
            ->addArgument('fb-user-id', InputArgument::REQUIRED, 'Facebook User ID of job offer author.')
            ->addOption('no-salary', null, InputOption::VALUE_OPTIONAL, 'Pass this option when you want to test offer without salary', false);
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $io = new SymfonyStyle($input, $output);

        $io->note('Job offer posted');

        $specialization = $this->system->query(SpecializationQuery::class)->findBySlug($input->getArgument('specialization'));

        if (!$specialization) {
            $io->error('Specialization does not exists.');

            return 1;
        }

        $noSalary = $input->getOption('no-salary') !== false;

        try {
            $this->system->handle(new PostToGroup(
                $specialization->slug(),
                $input->getArgument('fb-user-id'),
                new Offer(
                    new Company('Test sp. z o.o', 'https://test.com', 'Firma Test jest największa a zarazem najmniejsza firmą na świecie. Zatrudnia okolo 250 osób.'),
                    new Position('PHP Developer', 'Osoba na tym stanowisku będzie zajmować się developmentem php'),
                    new Location(false, 'Poland'),
                    $noSalary ? null : new Salary(1000, 5000, 'PLN', true),
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
            ));
        } catch (\Throwable $e) {
            $io->error('Can\'t post job offer at facebook group as a page. Please check logs for more details.');

            return 1;
        }

        return 0;
    }
}
