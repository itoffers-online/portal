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

use ITOffers\Offers\Application\Command\Twitter\TweetAboutOffer as TweetAboutOfferCommand;
use ITOffers\Offers\Application\FeatureToggle\TweetAboutOfferFeature;
use ITOffers\Offers\Offers;
use function mb_strtolower;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;
use Twig\Environment;

final class TweetAboutOffer extends Command
{
    public const NAME = 'offer:twitter:tweet';

    /**
     * @var string
     */
    protected static $defaultName = self::NAME;

    private Offers $offers;

    private SymfonyStyle $io;

    private Environment $twig;

    public function __construct(Offers $offers, Environment $twig)
    {
        parent::__construct();

        $this->offers = $offers;
        $this->twig = $twig;
    }

    protected function configure() : void
    {
        $this
            ->setDescription('Tweet about offer')
            ->addArgument('slug', InputArgument::REQUIRED, 'Offer slug')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output) : void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this->io->note('Tweet about offer');

        if ($input->isInteractive()) {
            $answer = $this->io->ask('Are you sure you want to tweet about this offer?', 'yes');

            if (mb_strtolower($answer) !== 'yes') {
                $this->io->note('Ok, offer was not posted');

                return 1;
            }
        }

        if (!$offer = $this->offers->offerQuery()->findBySlug($input->getArgument('slug'))) {
            $this->io->error(sprintf('Offer with slug "%s" does not exists.', $input->getArgument('slug')));

            return 1;
        }

        if (!$this->offers->featureQuery()->isEnabled(TweetAboutOfferFeature::NAME)) {
            $this->io->error('Tweeting about offers feature is currently disabled.');

            return 1;
        }

        try {
            $this->offers->handle(new TweetAboutOfferCommand(
                $offer->id()->toString(),
                $this->twig->render('@offers/twitter/offer.txt.twig', ['offer' => $offer]),
            ));
        } catch (Throwable $e) {
            $this->io->error('Can\'t remove offer, check logs for more details.');

            return 1;
        }

        $this->io->success('Offer removed');

        return 0;
    }
}
