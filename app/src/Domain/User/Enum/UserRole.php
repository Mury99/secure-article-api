<?php

declare(strict_types=1);

namespace App\Domain\User\Enum;

enum UserRole: string
{
    private const ROLE_PREFIX = 'ROLE_';

    case ADMIN = 'ADMIN';
    case AUTHOR = 'AUTHOR';
    case READER = 'READER';
    case USER = 'USER';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(fn (self $role) => $role->value, self::cases());
    }

    public function toSecurityRole(): string
    {
        return self::ROLE_PREFIX . $this->value;
    }
}
