<?php

declare(strict_types=1);

namespace App\Application\User\Handler;

use App\Application\User\Command\UserUpdateCommand;
use App\Application\User\Dto\AdminUserDto;
use App\Domain\User\Exception\EmailAlreadyTakenException;
use App\Domain\User\Repository\UserRepositoryInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

#[AsMessageHandler]
readonly class UserUpdateHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepositoryInterface $userRepository,
        private ObjectMapperInterface $objectMapper,
    ) {
    }

    public function __invoke(UserUpdateCommand $command): AdminUserDto
    {
        $user = $this->userRepository->findById($command->id);
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $user
            ->setEmail($command->email)
            ->setName($command->name)
            ->setRolesFromEnum($command->roles);

        try {
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new EmailAlreadyTakenException($command->email, $e);
        }

        return $this->objectMapper->map($user, AdminUserDto::class);
    }
}
