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

namespace App\Offers\Command\System;

use HireInSocial\Config;
use HireInSocial\Offers\Offers;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

final class PerformanceCheckCommand extends Command
{
    public const NAME = 'system:performance:check';

    /**
     * @var string
     */
    protected static $defaultName = self::NAME;

    /**
     * @var Offers
     */
    private $offers;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var SymfonyStyle
     */
    private $io;

    public function __construct(Offers $offers, Config $config)
    {
        parent::__construct();

        $this->offers = $offers;
        $this->config = $config;
    }

    protected function configure() : void
    {
        $this
            ->setDescription('Dump basic data about system bootstrap performance.')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output) : void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('system.bootstrap');
        $event = $stopwatch->stop('system.bootstrap');

        $this->io->title('System Performance');
        $this->io->text('Display basic metrics about single system bootstrap, those values can be used to calculated system total performance');
        $this->io->text('System bootstrap process is the same for CLI and Web however in web we need to include also Symfony bootstrap.');

        $this->io->table(
            ['Environment', 'Memory Usage'],
            [
                [$this->config->getString(Config::ENV), sprintf("%.2f MB", $event->getMemory() / 1048576)],
            ]
        );

        return 0;
    }
}
