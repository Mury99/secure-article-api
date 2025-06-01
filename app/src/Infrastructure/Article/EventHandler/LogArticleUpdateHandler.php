<?php

declare(strict_types=1);

namespace App\Infrastructure\Article\EventHandler;

use App\Domain\User\Event\ArticleUpdatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class LogArticleUpdateHandler
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(ArticleUpdatedEvent $event): void
    {
        $this->logger->info(sprintf('Article - %s was updated.', $event->getId()));
    }
}
