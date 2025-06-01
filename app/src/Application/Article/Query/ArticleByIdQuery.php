<?php

declare(strict_types=1);

namespace App\Application\Article\Query;

final class ArticleByIdQuery
{
    public function __construct(
        public string $id,
    ) {
    }
}
