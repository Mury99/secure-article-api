<?php

declare(strict_types=1);

namespace App\Application\User\Handler;

use App\Application\User\Dto\AdminUserDto;
use App\Application\User\Mapper\AdminUserDtoMapper;
use App\Application\User\Query\ListAdminUsersQuery;
use App\Domain\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ListAdminUsersQueryHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private AdminUserDtoMapper $dtoMapper,
    ) {
    }

    /**
     * @return AdminUserDto[]
     */
    public function __invoke(ListAdminUsersQuery $query): array
    {
        $users = $this->userRepository->findAll();

        return $this->dtoMapper->mapUsers($users);
    }
}
