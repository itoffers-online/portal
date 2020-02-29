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

namespace App\Offers\Command\Offer;

use ITOffers\Offers\Application\Query\Offer\OfferFilter;
use ITOffers\Offers\Offers;
use ITOffers\Offers\UserInterface\OfferThumbnail;
use function mb_strtolower;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ThumbnailRegenerate extends Command
{
    public const NAME = 'offer:thumbnail:regenerate';

    /**
     * @var string
     */
    protected static $defaultName = self::NAME;

    private Offers $offers;

    private OfferThumbnail $offerThumbnail;

    private SymfonyStyle $io;

    public function __construct(Offers $offers, OfferThumbnail $offerThumbnail)
    {
        parent::__construct();

        $this->offers = $offers;
        $this->offerThumbnail = $offerThumbnail;
    }

    protected function configure() : void
    {
        $this
            ->setDescription('Regenerate thumbnails for all active offers.')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output) : void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this->io->note('Regenerate offer thumbnails');

        if ($input->isInteractive()) {
            $answer = $this->io->ask('Are you sure you want to regenerate thumbnails for all active offers?', 'yes');

            if (mb_strtolower($answer) !== 'yes') {
                $this->io->note('Ok, action cancelled.');

                return 1;
            }
        }


        foreach ($offers = $this->offers->offerQuery()->findAll(OfferFilter::all()) as $offer) {
            $this->offerThumbnail->large($offer, true);
        }


        $this->io->success(\sprintf('Regenerated %d offer thumbnails', \count($offers)));

        return 0;
    }
}
