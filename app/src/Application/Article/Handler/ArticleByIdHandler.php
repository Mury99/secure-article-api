<?php

declare(strict_types=1);

namespace App\Application\Article\Handler;

use App\Application\Article\Query\ArticleByIdQuery;
use App\Domain\Article\Entity\Article;
use App\Domain\Article\Repository\ArticleRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ArticleByIdHandler
{
    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
    ) {
    }

    public function __invoke(ArticleByIdQuery $query): Article
    {
        $article = $this->articleRepository->findById($query->id);
        if (!$article) {
            throw new NotFoundHttpException('Article not found');
        }

        return $article;
    }
}
