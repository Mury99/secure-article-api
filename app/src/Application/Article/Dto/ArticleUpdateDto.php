<?php

declare(strict_types=1);

namespace App\Application\Article\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ArticleUpdateDto
{
    #[Assert\NotBlank]
    public ?string $title = null;

    #[Assert\NotBlank]
    public ?string $content = null;
}
