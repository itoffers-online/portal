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

namespace App\Offers\Command\User;

use HireInSocial\Offers\Application\Command\User\BlockUser as BlockUserCommand;
use HireInSocial\Offers\Offers;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

final class BlockUser extends Command
{
    public const NAME = 'user:block';

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
            ->setDescription('Block User')
            ->addArgument('slug', InputArgument::REQUIRED, 'Slug to the offer posted by user')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output) : void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this->io->note('Block user');

        if ($input->isInteractive()) {
            $answer = $this->io->ask('Are you sure you want block user?', 'yes');

            if (\mb_strtolower($answer) !== 'yes') {
                $this->io->note('Ok, action cancelled.');

                return 1;
            }
        }

        if (!$offer = $this->offers->offerQuery()->findBySlug($input->getArgument('slug'))) {
            $this->io->error(sprintf('Offer with slug "%s" does not exists.', $input->getArgument('slug')));

            return 1;
        }

        try {
            $this->offers->handle(new BlockUserCommand(
                $offer->userId()->toString()
            ));
        } catch (Throwable $e) {
            $this->io->error('Can\'t block user, check logs for more details.');

            return 1;
        }

        $this->io->success('User blocked');

        return 0;
    }
}
