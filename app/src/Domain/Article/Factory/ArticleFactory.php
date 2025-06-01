<?php

declare(strict_types=1);

namespace App\Domain\Article\Factory;

use App\Application\Article\Command\ArticleCreateCommand;
use App\Domain\Article\Entity\Article;
use App\Domain\User\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

readonly class ArticleFactory
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function createFromCommand(ArticleCreateCommand $command): Article
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $article = new Article();
        $article
            ->setAuthor($user)
            ->setTitle($command->title)
            ->setContent($command->content);

        return $article;
    }
}
