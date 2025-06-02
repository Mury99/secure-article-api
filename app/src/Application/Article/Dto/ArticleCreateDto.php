<?php

declare(strict_types=1);

namespace App\Application\Article\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ArticleCreateDto
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $title;

    #[Assert\NotBlank]
    public string $content;
}
