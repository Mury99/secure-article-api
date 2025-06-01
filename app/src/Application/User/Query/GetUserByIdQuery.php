<?php

declare(strict_types=1);

namespace App\Application\User\Query;

final readonly class GetUserByIdQuery
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
