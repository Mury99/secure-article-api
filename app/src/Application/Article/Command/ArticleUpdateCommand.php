<?php

declare(strict_types=1);

namespace App\Application\Article\Command;

final readonly class ArticleUpdateCommand
{
    public function __construct(
        public string $id,
        public string $title,
        public string $content,
    ) {
    }
}
