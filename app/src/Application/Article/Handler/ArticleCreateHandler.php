<?php

declare(strict_types=1);

namespace App\Application\Article\Handler;

use App\Application\Article\Command\ArticleCreateCommand;
use App\Application\Article\Dto\ArticleDto;
use App\Domain\Article\Factory\ArticleFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

#[AsMessageHandler]
readonly class ArticleCreateHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ObjectMapperInterface $objectMapper,
        private ArticleFactory $articleFactory,
    ) {
    }

    public function __invoke(ArticleCreateCommand $command): ArticleDto
    {
        $article = $this->articleFactory->createFromCommand($command);

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return $this->objectMapper->map($article, ArticleDto::class);
    }
}
