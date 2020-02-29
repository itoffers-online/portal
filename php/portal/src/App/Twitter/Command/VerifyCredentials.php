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

namespace App\Twitter\Command;

use Abraham\TwitterOAuth\TwitterOAuth;
use ITOffers\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class VerifyCredentials extends Command
{
    public const NAME = 'twitter:credentials:verify';

    /**
     * @var string
     */
    protected static $defaultName = self::NAME;

    private Config $config;

    private SymfonyStyle $io;

    public function __construct(Config $config)
    {
        parent::__construct();

        $this->config = $config;
    }

    protected function configure() : void
    {
        $this
            ->setDescription('Verify credentials')
            ->addArgument('oauth_token', InputArgument::REQUIRED, 'oAuthToken from previous command')
            ->addArgument('oauth_secret', InputArgument::REQUIRED, 'oAuthSecret from previous command')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output) : void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $connection = new TwitterOAuth(
            $this->config->getString(Config::TWITTER_API_KEY),
            $this->config->getString(Config::TWITTER_API_SECRET_KEY),
            $input->getArgument('oauth_token'),
            $input->getArgument('oauth_secret')
        );

        /** @var \stdClass $result */
        $result = $connection->get('account/verify_credentials', ['include_email' => true]);

        $this->io->table(
            ['property', 'value'],
            [
                ['id', $result->id],
                ['name', $result->name],
                ['screen_name', $result->screen_name],
                ['email', $result->email],
                ['url', 'https://twitter.com/' . $result->screen_name],
            ]
        );

        return 0;
    }
}
