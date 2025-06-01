<?php

declare(strict_types=1);

namespace App\Application\Article\Handler;

use App\Application\Article\Dto\ArticleDto;
use App\Application\Article\Query\ArticleListQuery;
use App\Domain\Article\Repository\ArticleRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

#[AsMessageHandler]
final readonly class ArticleListQueryHandler
{
    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
        private ObjectMapperInterface $objectMapper,
    ) {
    }

    public function __invoke(ArticleListQuery $query): \Generator
    {
        $articles = $this->articleRepository->findAll();

        foreach ($articles as $article) {
            yield $this->objectMapper->map($article, ArticleDto::class);
        }
    }
}
