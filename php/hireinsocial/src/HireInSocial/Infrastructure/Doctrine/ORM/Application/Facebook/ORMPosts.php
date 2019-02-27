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

namespace HireInSocial\Infrastructure\Doctrine\ORM\Application\Facebook;

use Doctrine\ORM\EntityManager;
use HireInSocial\Application\Facebook\Post;
use HireInSocial\Application\Facebook\Posts;

final class ORMPosts implements Posts
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(Post $post): void
    {
        $this->entityManager->persist($post);
    }
}
