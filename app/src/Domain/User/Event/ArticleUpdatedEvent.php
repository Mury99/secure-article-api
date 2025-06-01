<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

class ArticleUpdatedEvent
{
    public function __construct(
        public string $id,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }
}
