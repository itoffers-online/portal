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

namespace App\Twitter\Command;

use Abraham\TwitterOAuth\TwitterOAuth;
use HireInSocial\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class GetToken extends Command
{
    public const NAME = 'twitter:token:get';

    /**
     * @var string
     */
    protected static $defaultName = self::NAME;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var SymfonyStyle
     */
    private $io;

    public function __construct(Config $config)
    {
        parent::__construct();

        $this->config = $config;
    }

    protected function configure() : void
    {
        $this
            ->setDescription('Exchange pin and user oauth token/secret to account oauth token.')
            ->addArgument('pin', InputArgument::REQUIRED, 'PIN from twitter authentication flow')
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

        $accessTokenData = $connection->oauth("oauth/access_token", ["oauth_verifier" => $input->getArgument('pin')]);

        $this->io->note('oauth_token: ' . $accessTokenData['oauth_token']);
        $this->io->note('oauth_token_secret: ' . $accessTokenData['oauth_token_secret']);
        $this->io->note('user_id: ' . $accessTokenData['user_id']);

        return 0;
    }
}
