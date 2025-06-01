<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Controller;

use App\Application\User\Command\UserCreateCommand;
use App\Application\User\Dto\UserCreateDto;
use App\Domain\User\Exception\UserAlreadyExistsException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $messageBus,
    ) {
        $this->messageBus = $messageBus;
    }

    #[Route('/auth/register', methods: [Request::METHOD_POST])]
    #[Route('/users', name: 'users_create', methods: [Request::METHOD_POST])]
    public function __invoke(#[MapRequestPayload] UserCreateDto $dto): JsonResponse
    {
        try {
            $command = new UserCreateCommand($dto->username, $dto->password, $dto->name, $dto->getUserRoles());
            $user = $this->handle($command);

            return $this->json([
                'message' => 'User registered successfully',
                'user' => $user,
            ], Response::HTTP_CREATED);
        } catch (HandlerFailedException $e) {
            $previous = $e->getPrevious();
            if ($previous instanceof UserAlreadyExistsException) {
                return $this->json([
                    'message' => $previous->getMessage(),
                ], Response::HTTP_CONFLICT);
            }

            throw $e;
        }
    }
}
