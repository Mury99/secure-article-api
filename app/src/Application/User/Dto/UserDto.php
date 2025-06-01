<?php

declare(strict_types=1);

namespace App\Application\User\Dto;

use App\Domain\User\Entity\User;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(target: User::class)]
class UserDto
{
    public UuidInterface $id;
    public string $email;
    public string $name;
}
