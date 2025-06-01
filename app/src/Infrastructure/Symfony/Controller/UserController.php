<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Controller;

use App\Application\User\Command\UserDeleteCommand;
use App\Application\User\Command\UserUpdateCommand;
use App\Application\User\Dto\UserUpdateDto;
use App\Application\User\Query\GetUserByIdQuery;
use App\Application\User\Query\ListAdminUsersQuery;
use App\Domain\User\Exception\EmailAlreadyTakenException;
use App\Domain\User\Exception\UserNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/users', name: 'users_')]
class UserController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $messageBus,
    ) {
        $this->messageBus = $messageBus;
    }

    #[Route('', name: 'list', methods: [Request::METHOD_GET])]
    public function list(): JsonResponse
    {
        $users = $this->handle(new ListAdminUsersQuery());

        return $this->json($users);
    }

    #[Route('/{id}', name: 'detail', requirements: [
        'id' => Requirement::UUID_V7,
    ], methods: [Request::METHOD_GET])]
    public function show(string $id): JsonResponse
    {
        try {
            $user = $this->handle(new GetUserByIdQuery($id));

            return $this->json($user);
        } catch (HandlerFailedException $e) {
            $previous = $e->getPrevious();
            if ($previous instanceof UserNotFoundException) {
                return $this->json([
                    'message' => $previous->getMessage(),
                ], Response::HTTP_NOT_FOUND);
            }

            throw $e;
        }
    }

    #[Route('/{id}', name: 'update', requirements: [
        'id' => Requirement::UUID_V7,
    ], methods: [Request::METHOD_PUT])]
    public function update(
        string $id,
        #[MapRequestPayload] UserUpdateDto $dto,
    ): JsonResponse {
        $command = new UserUpdateCommand(
            $id,
            $dto->email,
            $dto->name,
            $dto->getUserRoles(),
        );

        try {
            $updatedUser = $this->handle($command);
        } catch (HandlerFailedException $e) {
            $previous = $e->getPrevious();
            if ($previous instanceof EmailAlreadyTakenException) {
                return $this->json([
                    'message' => $previous->getMessage(),
                ], Response::HTTP_NOT_FOUND);
            }

            throw $e;
        }

        return $this->json($updatedUser);
    }

    #[Route('/{id}', name: 'delete', requirements: [
        'id' => Requirement::UUID_V7,
    ], methods: [Request::METHOD_DELETE])]
    public function delete(string $id): JsonResponse
    {
        try {
            $this->handle(new UserDeleteCommand($id));
        } catch (HandlerFailedException $e) {
            $previous = $e->getPrevious();
            if ($previous instanceof UserNotFoundException) {
                return $this->json([
                    'message' => $previous->getMessage(),
                ], Response::HTTP_NOT_FOUND);
            }

            throw $e;
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
