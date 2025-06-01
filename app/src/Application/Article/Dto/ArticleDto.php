<?php

declare(strict_types=1);

namespace App\Application\Article\Dto;

use App\Application\User\Dto\UserDto;
use Ramsey\Uuid\UuidInterface;

class ArticleDto
{
    public UuidInterface $id;
    public string $title;
    public string $content;
    public UserDto $author;
    public \DateTimeImmutable $createdAt;
    public ?\DateTimeImmutable $updatedAt = null;
}
