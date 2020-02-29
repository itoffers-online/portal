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

namespace App\Offers\Command\Specialization;

use ITOffers\Offers\Application\Command\Specialization\SetTwitterChannel as SystemSetTwitterChannel;
use ITOffers\Offers\Offers;
use function mb_strtolower;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

final class SetTwitterChannel extends Command
{
    public const NAME = 'specialization:channel:twitter:set';

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
            ->setDescription('Set twitter channel for specialization.')
            ->addArgument('slug', InputArgument::REQUIRED, 'Specialization slug')
            ->addArgument('twitter_account_id', InputArgument::REQUIRED, 'Twitter account id')
            ->addArgument('twitter_screen_name', InputArgument::REQUIRED, 'Twitter screen name (used to generate url to profile)')
            ->addArgument('twitter_oauth_token', InputArgument::REQUIRED, 'Twitter account oAuth Token')
            ->addArgument('twitter_oauth_secret', InputArgument::REQUIRED, 'Twitter account oAuth Secret')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output) : void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this->io->note('Set specialization twitter channel');

        if ($input->isInteractive()) {
            $answer = $this->io->ask('Are you sure you want set twitter channel to the specialization?', 'yes');

            if (mb_strtolower($answer) !== 'yes') {
                $this->io->note('Ok, action cancelled.');

                return 1;
            }
        }

        if (!$this->offers->specializationQuery()->findBySlug($input->getArgument('slug'))) {
            $this->io->error(sprintf('twitter slug "%s" does not exists.', $input->getArgument('slug')));

            return 1;
        }

        try {
            $this->offers->handle(new SystemSetTwitterChannel(
                $input->getArgument('slug'),
                $input->getArgument('twitter_account_id'),
                $input->getArgument('twitter_screen_name'),
                $input->getArgument('twitter_oauth_token'),
                $input->getArgument('twitter_oauth_secret')
            ));
        } catch (Throwable $e) {
            $this->io->error('Can\'t set specialization twitter channel, check logs for more details.');

            return 1;
        }

        $this->io->success('Twitter channel added to the specialization');

        return 0;
    }
}
