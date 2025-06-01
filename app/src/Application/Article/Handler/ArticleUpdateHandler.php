<?php

declare(strict_types=1);

namespace App\Application\Article\Handler;

use App\Application\Article\Command\ArticleUpdateCommand;
use App\Application\Article\Dto\ArticleDto;
use App\Domain\Article\Repository\ArticleRepositoryInterface;
use App\Domain\User\Event\ArticleUpdatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

#[AsMessageHandler]
readonly class ArticleUpdateHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ArticleRepositoryInterface $articleRepository,
        private ObjectMapperInterface $objectMapper,
        private MessageBusInterface $eventBus,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(ArticleUpdateCommand $command): ArticleDto
    {
        $article = $this->articleRepository->findById($command->id);
        if (!$article) {
            throw new NotFoundHttpException('Article not found');
        }

        $article
            ->setTitle($command->title)
            ->setContent($command->content);

        $this->entityManager->flush();
        $this->eventBus->dispatch(new ArticleUpdatedEvent($command->id));

        return $this->objectMapper->map($article, ArticleDto::class);
    }
}
