<?php

declare(strict_types=1);

namespace App\Application\User\Handler;

use App\Application\User\Dto\AdminUserDto;
use App\Application\User\Query\GetUserByIdQuery;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

#[AsMessageHandler]
final readonly class GetUserByIdHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private ObjectMapperInterface $objectMapper,
    ) {
    }

    public function __invoke(GetUserByIdQuery $query): AdminUserDto
    {
        $id = $query->getId();
        $user = $this->userRepository->findById($id);

        if (!$user) {
            throw new UserNotFoundException($id);
        }

        return $this->objectMapper->map($user, AdminUserDto::class);
    }
}
