<?php

declare(strict_types=1);

namespace App\Application\User\Command;

use App\Domain\User\Enum\UserRole;

final readonly class UserUpdateCommand
{
    /**
     * @param non-empty-string $email
     * @param UserRole[]       $roles
     */
    public function __construct(
        public string $id,
        public string $email,
        public string $name,
        public array $roles,
    ) {
    }
}
