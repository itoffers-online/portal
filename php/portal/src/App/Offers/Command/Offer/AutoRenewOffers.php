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

use ITOffers\Offers\Application\Command\Offer\RenewOffer;
use ITOffers\Offers\Application\Query\User\Model\OfferAutoRenew;
use ITOffers\Offers\Offers;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class AutoRenewOffers extends Command
{
    public const NAME = 'offer:auto-renew:all';

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
            ->setDescription('Auto renew expired offers with assigned auto-renew')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output) : void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {

        $offersIds = \array_map(
            function (OfferAutoRenew $autoRenew) : string {
                return $autoRenew->offerId();
            },
            $this->offers->offerAutoRenewQuery()->findAllToRenew()
        );

        $totalRenewedOffers = 0;

        foreach ($offersIds as $offerId) {
            try {
                $this->offers->handle(new RenewOffer($offerId));
                $totalRenewedOffers += 1;

            } catch (\Throwable $e) {
                $this->io->error(\sprintf('Can\'t renew offer with id %s, please check logs for more details.', $offersIds));
            }
        }

        if ($totalRenewedOffers) {
            $this->io->success(\sprintf('Successfully renewed %d offers!', $totalRenewedOffers));
        } else {
            $this->io->note('There are no offers applicable for auto renew.');
        }

        return 0;
    }
}
