<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\User\Handler;

use App\Application\User\Command\UserUpdateCommand;
use App\Application\User\Dto\AdminUserDto;
use App\Application\User\Handler\UserUpdateHandler;
use App\Domain\User\Entity\User;
use App\Domain\User\Enum\UserRole;
use App\Domain\User\Exception\EmailAlreadyTakenException;
use App\Domain\User\Repository\UserRepositoryInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

class UserUpdateHandlerTest extends TestCase
{
    private MockObject $entityManager;
    private MockObject $userRepository;
    private MockObject $objectMapper;

    private UserUpdateHandler $handler;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->objectMapper = $this->createMock(ObjectMapperInterface::class);

        $this->handler = new UserUpdateHandler(
            $this->entityManager,
            $this->userRepository,
            $this->objectMapper
        );
    }

    public function testItUpdatesUserSuccessfully(): void
    {
        $user = $this->createMock(User::class);
        $command = new UserUpdateCommand(
            '01972d42-2789-7232-8804-5999923be4e4',
            'updated@example.com',
            'Updated Name',
            [UserRole::ADMIN]
        );

        $this->userRepository->expects($this->once())
            ->method('findById')
            ->with('01972d42-2789-7232-8804-5999923be4e4')
            ->willReturn($user);

        $user->expects($this->once())
            ->method('setEmail')
            ->with('updated@example.com')
            ->willReturnSelf();

        $user->expects($this->once())
            ->method('setName')
            ->with('Updated Name')
            ->willReturnSelf();

        $user->expects($this->once())
            ->method('setRolesFromEnum')
            ->with([UserRole::ADMIN])
            ->willReturnSelf();

        $this->entityManager->expects($this->once())
            ->method('flush');

        $expectedDto = $this->createMock(AdminUserDto::class);

        $this->objectMapper->expects($this->once())
            ->method('map')
            ->with($user, AdminUserDto::class)
            ->willReturn($expectedDto);

        $result = $this->handler->__invoke($command);

        $this->assertSame($expectedDto, $result);
    }

    public function testItThrowsWhenUserNotFound(): void
    {
        $command = new UserUpdateCommand(
            '01972d42-2789-7232-8804-5999923be4e4',
            'test@example.com',
            'Name',
            [UserRole::READER]
        );

        $this->userRepository->expects($this->once())
            ->method('findById')
            ->with('01972d42-2789-7232-8804-5999923be4e4')
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);

        $this->handler->__invoke($command);
    }

    public function testItThrowsWhenEmailAlreadyTaken(): void
    {
        $user = $this->createMock(User::class);
        $command = new UserUpdateCommand(
            '01972d42-2789-7232-8804-5999923be4e4',
            'taken@example.com',
            'Name',
            [UserRole::READER]
        );

        $this->userRepository->expects($this->once())
            ->method('findById')
            ->with('01972d42-2789-7232-8804-5999923be4e4')
            ->willReturn($user);

        $user->expects($this->once())
            ->method('setEmail')
            ->willReturnSelf();

        $user->expects($this->once())
            ->method('setName')
            ->willReturnSelf();

        $user->expects($this->once())
            ->method('setRolesFromEnum')
            ->willReturnSelf();

        $this->entityManager->expects($this->once())
            ->method('flush')
            ->willThrowException(
                $this->createMock(UniqueConstraintViolationException::class)
            );

        $this->expectException(EmailAlreadyTakenException::class);

        $this->handler->__invoke($command);
    }
}
