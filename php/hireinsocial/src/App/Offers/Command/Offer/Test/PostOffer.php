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

namespace App\Offers\Command\Offer\Test;

use Faker\Factory;
use HireInSocial\Notifications\Notifications;
use HireInSocial\Offers\Application\Command\Offer\Offer\Company;
use HireInSocial\Offers\Application\Command\Offer\Offer\Contact;
use HireInSocial\Offers\Application\Command\Offer\Offer\Contract;
use HireInSocial\Offers\Application\Command\Offer\Offer\Description;
use HireInSocial\Offers\Application\Command\Offer\Offer\Location;
use HireInSocial\Offers\Application\Command\Offer\Offer\Location\LatLng;
use HireInSocial\Offers\Application\Command\Offer\Offer\Offer;
use HireInSocial\Offers\Application\Command\Offer\Offer\Position;
use HireInSocial\Offers\Application\Command\Offer\Offer\Salary;
use HireInSocial\Offers\Application\Command\Offer\PostOffer as SystemPostOffer;
use HireInSocial\Offers\Application\Command\User\FacebookConnect;
use HireInSocial\Offers\Application\Query\Offer\Model\Offer\Salary as SalaryView;
use HireInSocial\Offers\Offers;
use Ramsey\Uuid\Uuid;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

final class PostOffer extends Command
{
    public const NAME = 'offer:post:test';

    /**
     * @var string
     */
    protected static $defaultName = self::NAME;

    /**
     * @var Offers
     */
    private $offers;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var Notifications
     */
    private $notifications;

    public function __construct(Offers $offers, string $locale)
    {
        parent::__construct();

        $this->offers = $offers;
        $this->locale = $locale;
    }

    protected function configure() : void
    {
        $this
            ->setDescription('Test posting job offer with automatically generated fake data. This offer also generate fake user.')
            ->addArgument('specialization', InputArgument::REQUIRED, 'Specialization slug where for which test offer should be posted.')
            ->addOption('title', null, InputOption::VALUE_OPTIONAL, 'Offer title', 'Software Developer')
            ->addOption('no-salary', null, InputOption::VALUE_OPTIONAL, 'Pass this option when you want to test offer without salary', false)
            ->addOption('offer-pdf', null, InputOption::VALUE_OPTIONAL, 'Path to offer PDF file.')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output) : void
    {
        $this->io = new SymfonyStyle($input, $output);

        $offerPDFPath = (string) $input->getOption('offer-pdf');

        if ($offerPDFPath && !\file_exists($offerPDFPath)) {
            throw new RuntimeException(sprintf('Offer PDF "%s" file does not exists.', $offerPDFPath));
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this->io->note('Job offer posted');

        $specialization = $this->offers->specializationQuery()->findBySlug($input->getArgument('specialization'));

        if (!$specialization) {
            $this->io->error('Specialization does not exists.');

            return 1;
        }

        $noSalary = $input->getOption('no-salary') !== false;
        $offerPDFpath = $input->getOption('offer-pdf');

        try {
            $faker = Factory::create($this->locale);

            $fbUserAppId = $faker->uuid;
            $email = $faker->email;

            $this->offers->handle(new FacebookConnect($fbUserAppId, $email));

            $user = $this->offers->userQuery()->findByFacebook($fbUserAppId);

            $this->offers->handle(new SystemPostOffer(
                $offerId = Uuid::uuid4()->toString(),
                $specialization->slug(),
                'en_US',
                $user->id(),
                new Offer(
                    new Company(
                        'itoffers.online',
                        'https://itoffers.online',
                        'itoffers.online is recruiting portal that connects recruiters with candidates'
                    ),
                    new Position(
                        \random_int(0, 4),
                        $input->getOption('title'),
                        'Full stack Software developer position, you will work mostly on web applications with automated and scalable infrastructure.'
                    ),
                    new Location($faker->boolean, $faker->countryCode, $faker->city, new LatLng(50.16212, 19.9353153)),
                    $noSalary ? null : new Salary($faker->numberBetween(1000, 5000), $faker->numberBetween(5000, 20000), 'PLN', $faker->boolean, SalaryView::PERIOD_TYPE_MONTH),
                    new Contract('Contract'),
                    new Description(
                        'We don\'t have strict number of days off, you take as much as you need, you can work remotely or in the office',
                        new Description\Requirements(
                            'Candidate for this position needs to be solid, reliable and meet all our expectations. You need to have at least 5 years of commercial experience.',
                            ...$this->generateSkills()
                        )
                    ),
                    new Contact(
                        'contact@hirein.social',
                        'Hire Manager',
                        '+1 333333333'
                    ),
                ),
                $offerPDFpath
            ));
        } catch (Throwable $e) {
            $this->io->error($e->getMessage());
            $this->io->error('Can\'t post job offer at facebook group as a page. Please check logs for more details.');

            return 1;
        }

        return 0;
    }

    /**
     * @return Description\Requirements\Skill[]
     * @throws \Exception
     */
    protected function generateSkills() : array
    {
        $skills = ['php', 'git', 'js', 'jenkins', 'terraform', 'ansible', 'elixir', 'mongo', 'postgresql', 'mysql'];
        $randomSkills = \array_unique(
            \array_map(
                function (int $i) use ($skills) {
                    return $skills[\random_int(0, \count($skills) - 1)];
                },
                \range(0, \random_int(0, 5))
            )
        );

        return \array_map(
            function (string $skill) {
                return new Description\Requirements\Skill(
                    $skill,
                    (bool) \random_int(0, 1),
                    (bool) \random_int(0, 1) ? \random_int(1, 10) : null
                );
            },
            $randomSkills
        );
    }
}
