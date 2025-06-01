<?php

declare(strict_types=1);

namespace App\Domain\Article\Repository;

use App\Domain\Article\Entity\Article;

interface ArticleRepositoryInterface
{
    public function findById(string $id): ?Article;

    /**
     * @return list<Article>
     */
    public function findAll(): array;
}
