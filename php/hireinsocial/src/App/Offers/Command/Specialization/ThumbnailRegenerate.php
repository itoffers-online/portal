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

namespace App\Offers\Command\Specialization;

use HireInSocial\Offers\Offers;
use HireInSocial\Offers\UserInterface\SpecializationThumbnail;
use function mb_strtolower;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ThumbnailRegenerate extends Command
{
    public const NAME = 'specialization:thumbnail:regenerate';

    /**
     * @var string
     */
    protected static $defaultName = self::NAME;

    /**
     * @var Offers
     */
    private $offers;

    /**
     * @var SpecializationThumbnail
     */
    private $specializationThumbnail;

    /**
     * @var SymfonyStyle
     */
    private $io;

    public function __construct(Offers $offers, SpecializationThumbnail $specializationThumbnail)
    {
        parent::__construct();

        $this->offers = $offers;
        $this->specializationThumbnail = $specializationThumbnail;
    }

    protected function configure() : void
    {
        $this
            ->setDescription('Regenerate thumbnails for all specializations.')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output) : void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this->io->note('Regenerate specialization thumbnails');

        if ($input->isInteractive()) {
            $answer = $this->io->ask('Are you sure you want to regenerate thumbnails for all specializations?', 'yes');

            if (mb_strtolower($answer) !== 'yes') {
                $this->io->note('Ok, action cancelled.');

                return 1;
            }
        }


        foreach ($specializations = $this->offers->specializationQuery()->all() as $specialization) {
            $this->specializationThumbnail->large($specialization, true);
        }


        $this->io->success(\sprintf('Regenerated %d specialization thumbnails', \count($specializations)));

        return 0;
    }
}
