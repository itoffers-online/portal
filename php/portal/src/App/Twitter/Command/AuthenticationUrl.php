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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class AuthenticationUrl extends Command
{
    public const NAME = 'twitter:auth-url';

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
            ->setDescription('Displays authentication url and oauth token/secret required to obtain user access token.')
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
            $this->config->getString(Config::TWITTER_ACCESS_TOKEN),
            $this->config->getString(Config::TWITTER_ACCESS_TOKEN_SECRET)
        );

        $accessTokenData = $connection->oauth("oauth/request_token", ["oauth_callback" => "oob"]);


        $this->io->note('oauth_token: ' . $accessTokenData['oauth_token']);
        $this->io->note('oauth_secret: ' . $accessTokenData['oauth_token_secret']);

        $url = $connection->url("oauth/authorize", ["oauth_token" => $accessTokenData['oauth_token']]);

        $this->io->note($url);

        return 0;
    }
}
