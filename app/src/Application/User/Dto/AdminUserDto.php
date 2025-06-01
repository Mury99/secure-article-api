<?php

declare(strict_types=1);

namespace App\Application\User\Dto;

use Ramsey\Uuid\UuidInterface;

class AdminUserDto
{
    public UuidInterface $id;
    public string $email;
    public string $name;

    /**
     * @var list<string>
     */
    public array $roles;
}
