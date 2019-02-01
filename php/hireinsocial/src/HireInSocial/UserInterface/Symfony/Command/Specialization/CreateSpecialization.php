<?php

declare (strict_types=1);

namespace HireInSocial\UserInterface\Symfony\Command\Specialization;

use HireInSocial\Application\Query\Specialization\SpecializationQuery;
use HireInSocial\Application\System;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use HireInSocial\Application\Command\Specialization\CreateSpecialization as SystemCreateSpecializationCommand;

final class CreateSpecialization extends Command
{
    public const NAME = 'specialization:create';
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
            ->setDescription('<info>[Specialization]</info> Create new specialization.')
            ->addArgument('slug', InputArgument::REQUIRED, 'Specialization slug')
            ->addArgument('name', InputArgument::REQUIRED, 'Specialization name')
            ->addArgument('facebook_page_id', InputArgument::REQUIRED, 'Facebook page id that will post offers into group')
            ->addArgument('facebook_page_token', InputArgument::REQUIRED, 'Facebook page id access token with publish_to_groups permission')
            ->addArgument('facebook_group_id', InputArgument::REQUIRED, 'Facebook group id where page will post offers')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $io = new SymfonyStyle($input, $output);

        $io->note('Create new specialization');

        if ($input->isInteractive()) {
            $answer = $io->ask('Are you sure you want to create this specialization?', 'yes');

            if (\mb_strtolower($answer) !== 'yes') {
                $io->note('Ok, specialization was not created');

                return 1;
            }
        }

        if ($this->system->query(SpecializationQuery::class)->findBySlug($input->getArgument('slug'))) {
            $io->error(sprintf('Specialization slug "%s" already exists.', $input->getArgument('slug')));

            return 1;
        }

        try {
            $this->system->handle(new SystemCreateSpecializationCommand(
                $input->getArgument('slug'),
                $input->getArgument('name'),
                $input->getArgument('facebook_page_id'),
                $input->getArgument('facebook_page_token'),
                $input->getArgument('facebook_group_id')
            ));

        } catch (\Throwable $e) {
            $io->error('Can\'t crete specialization, check logs for more details.');

            return 1;
        }

        $io->success('Specialization created');

        return 0;
    }

}