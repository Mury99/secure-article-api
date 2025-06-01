<?php

declare(strict_types=1);

namespace App\Application\Article\Command;

class ArticleCreateCommand
{
    public function __construct(
        public string $content,
        public string $title,
    ) {
    }
}
