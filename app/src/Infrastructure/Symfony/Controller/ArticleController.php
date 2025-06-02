<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Controller;

use App\Application\Article\Command\ArticleCreateCommand;
use App\Application\Article\Command\ArticleDeleteCommand;
use App\Application\Article\Command\ArticleUpdateCommand;
use App\Application\Article\Dto\ArticleCreateDto;
use App\Application\Article\Dto\ArticleDto;
use App\Application\Article\Dto\ArticleUpdateDto;
use App\Application\Article\Query\ArticleByIdQuery;
use App\Application\Article\Query\ArticleListQuery;
use App\Infrastructure\Article\Security\Symfony\ArticleVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/articles', name: 'articles_')]
class ArticleController extends AbstractController
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
        $this->denyAccessUnlessGranted(ArticleVoter::VIEW, null);

        $articles = $this->handle(new ArticleListQuery());

        return $this->json($articles);
    }

    #[Route('', name: 'create', methods: [Request::METHOD_POST])]
    public function create(#[MapRequestPayload] ArticleCreateDto $dto): JsonResponse
    {
        $this->denyAccessUnlessGranted(ArticleVoter::CREATE, null);

        $command = new ArticleCreateCommand($dto->title, $dto->content);
        $article = $this->handle($command);

        return $this->json($article, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'detail', requirements: [
        'id' => Requirement::UUID_V7,
    ], methods: [Request::METHOD_GET])]
    public function show(string $id, ObjectMapperInterface $objectMapper): JsonResponse
    {
        $this->denyAccessUnlessGranted(ArticleVoter::VIEW, null);

        try {
            $article = $this->handle(new ArticleByIdQuery($id));

            return $this->json($objectMapper->map($article, ArticleDto::class));
        } catch (HandlerFailedException $e) {
            $previous = $e->getPrevious();
            if ($previous instanceof NotFoundHttpException) {
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
        #[MapRequestPayload] ArticleUpdateDto $dto,
    ): JsonResponse {
        try {
            $article = $this->handle(new ArticleByIdQuery($id));
        } catch (HandlerFailedException $e) {
            $previous = $e->getPrevious();
            if ($previous instanceof NotFoundHttpException) {
                return $this->json([
                    'message' => $previous->getMessage(),
                ], Response::HTTP_NOT_FOUND);
            }

            throw $e;
        }

        $this->denyAccessUnlessGranted(ArticleVoter::EDIT, $article);

        $command = new ArticleUpdateCommand(
            $id,
            $dto->title,
            $dto->content,
        );
        $updatedArticle = $this->handle($command);

        return $this->json($updatedArticle);
    }

    #[Route('/{id}', name: 'delete', requirements: [
        'id' => Requirement::UUID_V7,
    ], methods: [Request::METHOD_DELETE])]
    public function delete(string $id): JsonResponse
    {
        try {
            $article = $this->handle(new ArticleByIdQuery($id));
        } catch (HandlerFailedException $e) {
            $previous = $e->getPrevious();
            if ($previous instanceof NotFoundHttpException) {
                return $this->json([
                    'message' => $previous->getMessage(),
                ], Response::HTTP_NOT_FOUND);
            }

            throw $e;
        }

        $this->denyAccessUnlessGranted(ArticleVoter::DELETE, $article);
        $this->handle(new ArticleDeleteCommand($id));

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
