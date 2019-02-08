<?php

declare(strict_types=1);

namespace HireInSocial\UserInterface\Symfony\Command\Facebook\Page;

use Faker\Factory;
use HireInSocial\Application\Command\Facebook\Page\PostToGroup;
use HireInSocial\Application\Command\Offer\Company;
use HireInSocial\Application\Command\Offer\Contact;
use HireInSocial\Application\Command\Offer\Contract;
use HireInSocial\Application\Command\Offer\Description;
use HireInSocial\Application\Command\Offer\Location;
use HireInSocial\Application\Command\Offer\Offer;
use HireInSocial\Application\Command\Offer\Position;
use HireInSocial\Application\Command\Offer\Salary;
use HireInSocial\Application\Command\Throttle\RemoveThrottle;
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
    private $locale;

    public function __construct(System $system, string $locale)
    {
        parent::__construct();

        $this->system = $system;
        $this->locale = $locale;
    }

    protected function configure() : void
    {
        $this
            ->setDescription('<info>[Facebook]</info> Post test job offer at Facebook group as a page.')
            ->addArgument('specialization', InputArgument::REQUIRED, 'Specialization slug where for which test offer should be posted.')
            ->addArgument('fb-user-id', InputArgument::REQUIRED, 'Facebook User ID of job offer author.')
            ->addOption('no-salary', null, InputOption::VALUE_OPTIONAL, 'Pass this option when you want to test offer without salary', false)
            ->addOption('remove-throttle', null, InputOption::VALUE_OPTIONAL, 'Remove throttle after posting offer to a group in order to repeat command quickly', false)
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
        $removeThrottle = $input->getOption('remove-throttle') !== false;

        try {
            $faker = Factory::create($this->locale);

            $this->system->handle(new PostToGroup(
                $specialization->slug(),
                $input->getArgument('fb-user-id'),
                new Offer(
                    new Company($faker->company, $faker->url, $faker->text(512)),
                    new Position('PHP Developer', $faker->text(1024)),
                    new Location($faker->boolean, $faker->country),
                    $noSalary ? null : new Salary($faker->numberBetween(1000, 5000), $faker->numberBetween(5000, 20000), 'PLN', $faker->boolean),
                    new Contract('B2B'),
                    new Description(
                        $faker->text(1024),
                        $faker->text(1024)
                    ),
                    new Contact(
                        $faker->email,
                        $faker->name,
                        trim($faker->phoneNumber)
                    )
                )
            ));
        } catch (\Throwable $e) {
            $io->error('Can\'t post job offer at facebook group as a page. Please check logs for more details.');

            return 1;
        }

        if ($removeThrottle) {
            $this->system->handle(new RemoveThrottle($input->getArgument('fb-user-id')));
        }

        return 0;
    }
}
