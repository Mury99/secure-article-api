<?php

declare(strict_types=1);

namespace App\Application\User\Command;

use App\Domain\User\Enum\UserRole;

class UserCreateCommand
{
    /**
     * @param non-empty-string $username
     * @param UserRole[]       $roles
     */
    public function __construct(
        public string $username,
        #[\SensitiveParameter] public string $password,
        public string $name,
        public array $roles,
    ) {
    }
}
