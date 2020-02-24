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

use ITOffers\Offers\Application\Command\User\AddOfferAutoRenews as AddOfferAutoRenewsCommand;
use ITOffers\Offers\Application\Query\Offer\OfferFilter;
use ITOffers\Offers\Offers;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

final class AddOfferAutoRenews extends Command
{
    public const NAME = 'user:offer-auto-renew:add';

    /**
     * @var string
     */
    protected static $defaultName = self::NAME;

    /**
     * @var Offers
     */
    private $offers;

    /**
     * @var SymfonyStyle
     */
    private $io;

    public function __construct(Offers $offers)
    {
        parent::__construct();

        $this->offers = $offers;
    }

    protected function configure() : void
    {
        $this
            ->setDescription('Add offer auto renews to user')
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('count', InputArgument::REQUIRED, 'Number of offer auto renews')
            ->addArgument('expiresInDays', InputArgument::REQUIRED, 'Number of days after extra offers expires')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output) : void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this->io->note('Add offer auto renews');

        $user = $this->offers->userQuery()->findByEmail($input->getArgument('email'));

        if (!$user) {
            $this->io->error(\sprintf('User with email "%s" does not exists', $input->getArgument('email')));

            return 1;
        }

        $this->io->note('User offer auto renews: ' . $this->offers->offerAutoRenewQuery()->countUnassignedNotExpired($user->id()));
        $this->io->note('User offers: ' . $this->offers->offerQuery()->count(OfferFilter::all()->belongsTo($user->id())));

        if ($input->isInteractive()) {
            $answer = $this->io->ask('Are you sure you want add user offer auto renews?', 'yes');

            if (\mb_strtolower($answer) !== 'yes') {
                $this->io->note('Ok, action cancelled.');

                return 1;
            }
        }

        try {
            $this->offers->handle(
                new AddOfferAutoRenewsCommand(
                    $user->id(),
                    (int) $input->getArgument('count'),
                    (int) $input->getArgument('expiresInDays')
                )
            );
        } catch (Throwable $e) {
            $this->io->error('Can\'t add offer auto renews, check logs for more details.');

            return 1;
        }

        $this->io->success('User received offer auto renews');

        return 0;
    }
}
