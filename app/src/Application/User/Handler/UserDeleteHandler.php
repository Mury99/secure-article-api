<?php

declare(strict_types=1);

namespace App\Application\User\Handler;

use App\Application\User\Command\UserDeleteCommand;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UserDeleteHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(UserDeleteCommand $command): void
    {
        $id = $command->getId();
        $user = $this->userRepository->findById($id);

        if (!$user) {
            throw new UserNotFoundException($id);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}
