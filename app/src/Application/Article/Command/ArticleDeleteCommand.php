<?php

declare(strict_types=1);

namespace App\Application\Article\Command;

final class ArticleDeleteCommand
{
    public function __construct(
        public string $id,
    ) {
    }
}
