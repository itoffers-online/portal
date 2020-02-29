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

namespace App\Offers\Command\User;

use ITOffers\Offers\Application\Command\User\AddExtraOffers as AddExtraOffersCommand;
use ITOffers\Offers\Application\Query\Offer\OfferFilter;
use ITOffers\Offers\Offers;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

final class AddExtraOffers extends Command
{
    public const NAME = 'user:extra-offers:add';

    /**
     * @var string
     */
    protected static $defaultName = self::NAME;

    private Offers $offers;

    private SymfonyStyle $io;

    public function __construct(Offers $offers)
    {
        parent::__construct();

        $this->offers = $offers;
    }

    protected function configure() : void
    {
        $this
            ->setDescription('Add extra offers to user')
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('count', InputArgument::REQUIRED, 'Number of extra offers')
            ->addArgument('expiresInDays', InputArgument::REQUIRED, 'Number of days after extra offers expires')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output) : void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this->io->note('Add extra offers');

        $user = $this->offers->userQuery()->findByEmail($input->getArgument('email'));

        if (!$user) {
            $this->io->error(\sprintf('User with email "%s" does not exists', $input->getArgument('email')));

            return 1;
        }

        $this->io->note('User extra offers: ' . $this->offers->extraOffersQuery()->countNotExpired($user->id()));
        $this->io->note('User offers: ' . $this->offers->offerQuery()->count(OfferFilter::all()->belongsTo($user->id())));
        $this->io->note('User throttled: ' . ($this->offers->offerThrottleQuery()->isThrottled($user->id()) ? 'yes' : 'no'));

        if ($input->isInteractive()) {
            $answer = $this->io->ask('Are you sure you want add user extra offers?', 'yes');

            if (\mb_strtolower($answer) !== 'yes') {
                $this->io->note('Ok, action cancelled.');

                return 1;
            }
        }


        try {
            $this->offers->handle(
                new AddExtraOffersCommand(
                    $user->id(),
                    (int) $input->getArgument('count'),
                    (int) $input->getArgument('expiresInDays')
                )
            );
        } catch (Throwable $e) {
            $this->io->error('Can\'t grant extra offers, check logs for more details.');

            return 1;
        }

        $this->io->success('User received extra offers');

        return 0;
    }
}
