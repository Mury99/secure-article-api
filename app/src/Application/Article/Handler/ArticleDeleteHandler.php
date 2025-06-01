<?php

declare(strict_types=1);

namespace App\Application\Article\Handler;

use App\Application\Article\Command\ArticleDeleteCommand;
use App\Domain\Article\Repository\ArticleRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ArticleDeleteHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ArticleRepositoryInterface $articleRepository,
    ) {
    }

    public function __invoke(ArticleDeleteCommand $command): void
    {
        $article = $this->articleRepository->findById($command->id);
        if (!$article) {
            throw new NotFoundHttpException('Article not found');
        }

        $this->entityManager->remove($article);
        $this->entityManager->flush();
    }
}
