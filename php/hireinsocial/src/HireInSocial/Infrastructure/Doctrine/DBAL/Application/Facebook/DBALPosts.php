<?php

declare(strict_types=1);

namespace HireInSocial\Infrastructure\Doctrine\DBAL\Application\Facebook;

use Doctrine\DBAL\Connection;
use HireInSocial\Application\Facebook\Post;
use HireInSocial\Application\Facebook\Posts;

final class DBALPosts implements Posts
{
    public const TABLE_NAME = 'his_facebook_post';

    public const FIELD_FB_ID = 'fb_id';
    public const FIELD_FB_AUTHOR_ID = 'fb_author_id';
    public const FIELD_JOB_OFFER_ID = 'job_offer_id';
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function add(Post $post): void
    {
        $this->connection->insert(
            self::TABLE_NAME,
            [
                self::FIELD_FB_ID => $post->fbId(),
                self::FIELD_FB_AUTHOR_ID => $post->authorId(),
                self::FIELD_JOB_OFFER_ID => $post->jobOfferId(),
            ]
        );
    }
}
