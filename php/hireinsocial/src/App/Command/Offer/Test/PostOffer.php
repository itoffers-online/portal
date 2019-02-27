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

namespace App\Command\Offer\Test;

use Faker\Factory;
use HireInSocial\Application\Command\Offer\Offer\Channels;
use HireInSocial\Application\Command\Offer\Offer\Company;
use HireInSocial\Application\Command\Offer\Offer\Contact;
use HireInSocial\Application\Command\Offer\Offer\Contract;
use HireInSocial\Application\Command\Offer\Offer\Description;
use HireInSocial\Application\Command\Offer\Offer\Location;
use HireInSocial\Application\Command\Offer\Offer\Offer;
use HireInSocial\Application\Command\Offer\Offer\Position;
use HireInSocial\Application\Command\Offer\Offer\Salary;
use HireInSocial\Application\Command\Offer\PostOffer as SystemPostOffer;
use HireInSocial\Application\Command\Throttle\RemoveThrottle;
use HireInSocial\Application\Command\User\FacebookConnect;
use HireInSocial\Application\Query\Specialization\SpecializationQuery;
use HireInSocial\Application\Query\User\UserQuery;
use HireInSocial\Application\System;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class PostOffer extends Command
{
    public const NAME = 'post:offer:test';
    protected static $defaultName = self::NAME;

    private $system;
    private $locale;
    /**
     * @var SymfonyStyle
     */
    private $io;

    public function __construct(System $system, string $locale)
    {
        parent::__construct();

        $this->system = $system;
        $this->locale = $locale;
    }

    protected function configure() : void
    {
        $this
            ->setDescription('<info>[Offer]</info> Test posting job offer with automatically generated fake data. This offer also generate fake user.')
            ->addArgument('specialization', InputArgument::REQUIRED, 'Specialization slug where for which test offer should be posted.')
            ->addOption('no-salary', null, InputOption::VALUE_OPTIONAL, 'Pass this option when you want to test offer without salary', false)
            ->addOption('remove-throttle', null, InputOption::VALUE_OPTIONAL, 'Remove throttle after posting offer to a group in order to repeat command quickly', false)
            ->addOption('post-facebook-group', null, InputOption::VALUE_OPTIONAL, 'Post offer to facebook group assigned to the specialization', false)
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this->io->note('Job offer posted');

        $specialization = $this->system->query(SpecializationQuery::class)->findBySlug($input->getArgument('specialization'));

        if (!$specialization) {
            $this->io->error('Specialization does not exists.');

            return 1;
        }

        $noSalary = $input->getOption('no-salary') !== false;
        $removeThrottle = $input->getOption('remove-throttle') !== false;
        $postFacebookGroup = $input->getOption('post-facebook-group') !== false;

        try {
            $faker = Factory::create($this->locale);

            $fbUserAppId = $faker->uuid;

            $this->system->handle(new FacebookConnect($fbUserAppId));

            $user = $this->system->query(UserQuery::class)->findByFacebook($fbUserAppId);

            $this->system->handle(new SystemPostOffer(
                $specialization->slug(),
                $user->id(),
                new Offer(
                    new Company($faker->company, $faker->url, $faker->text(512)),
                    new Position('Developer', $faker->text(1024)),
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
                        '+1 333333333'
                    ),
                    new Channels($postFacebookGroup)
                )
            ));
        } catch (\Throwable $e) {
            $this->io->error('Can\'t post job offer at facebook group as a page. Please check logs for more details.');

            return 1;
        }

        if ($removeThrottle) {
            $this->system->handle(new RemoveThrottle($user->id()));
        }

        return 0;
    }
}
