<?php

declare(strict_types=1);

namespace App\Application\User\Handler;

use App\Application\User\Command\UserCreateCommand;
use App\Application\User\Dto\AdminUserDto;
use App\Domain\User\Exception\UserAlreadyExistsException;
use App\Domain\User\Factory\UserFactory;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

#[AsMessageHandler]
final readonly class UserCreateHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserFactory $userFactory,
        private ObjectMapperInterface $objectMapper,
    ) {
    }

    /**
     * @throws UserAlreadyExistsException
     */
    public function __invoke(UserCreateCommand $command): AdminUserDto
    {
        $user = $this->userFactory->createFromCommand($command);
        $this->entityManager->persist($user);

        try {
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new UserAlreadyExistsException('User already exists.', previous: $e);
        }

        return $this->objectMapper->map($user, AdminUserDto::class);
    }
}
