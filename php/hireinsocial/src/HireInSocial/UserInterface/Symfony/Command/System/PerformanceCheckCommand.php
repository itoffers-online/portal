<?php

declare(strict_types=1);

namespace HireInSocial\UserInterface\Symfony\Command\System;

use HireInSocial\Application\System;
use HireInSocial\Application\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

final class PerformanceCheckCommand extends Command
{
    public const NAME = 'system:performance:check';
    protected static $defaultName = self::NAME;

    private $system;
    private $config;

    public function __construct(System $system, Config $config)
    {
        parent::__construct();

        $this->system = $system;
        $this->config = $config;
    }

    protected function configure() : void
    {
        $this
            ->setDescription('<info>[System]</info> Dump basic data about system bootstrap performance.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $io = new SymfonyStyle($input, $output);

        $stopwatch = new Stopwatch();
        $stopwatch->start('system.bootstrap');
        $event = $stopwatch->stop('system.bootstrap');

        $io->title('System Performance');
        $io->text('Display basic metrics about single system bootstrap, those values can be used to calculated system total performance');
        $io->text('System bootstrap process is the same for CLI and Web however in web we need to include also Symfony bootstrap.');

        $io->table(
            ['Environment', 'Memory Usage'],
            [
                [$this->config->getString(Config::ENV), sprintf("%.2f MB", $event->getMemory() / 1048576)],
            ]
        );

        return 0;
    }
}
