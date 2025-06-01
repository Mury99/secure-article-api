<?php

declare(strict_types=1);

namespace App\Application\User\Dto;

use App\Domain\User\Enum\UserRole;
use Symfony\Component\Validator\Constraints as Assert;

class UserUpdateDto
{
    /**
     * @var non-empty-string
     */
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    public string $name;

    /**
     * @var string[]
     */
    #[Assert\All([
        new Assert\Choice(callback: [UserRole::class, 'values']),
    ])]
    public array $roles = [];

    /**
     * @return UserRole[]
     */
    public function getUserRoles(): array
    {
        return array_map(fn (string $role) => UserRole::from($role), $this->roles);
    }
}
