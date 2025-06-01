<?php

declare(strict_types=1);

namespace App\Application\User\Mapper;

use App\Application\User\Dto\AdminUserDto;
use App\Domain\User\Entity\User;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

readonly class AdminUserDtoMapper
{
    public function __construct(
        private ObjectMapperInterface $objectMapper,
    ) {
    }

    /**
     * @param User[] $users
     *
     * @return AdminUserDto[]
     */
    public function mapUsers(iterable $users): array
    {
        return array_map(
            fn (User $user) => $this->objectMapper->map($user, AdminUserDto::class),
            is_array($users) ? $users : iterator_to_array($users)
        );
    }
}
