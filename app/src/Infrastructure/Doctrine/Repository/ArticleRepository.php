<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Article\Entity\Article;
use App\Domain\Article\Repository\ArticleRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository implements ArticleRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findById(string $id): ?Article
    {
        return $this->findOneBy([
            'id' => $id,
        ]);
    }

    public function findAll(): array
    {
        return $this->findBy([]);
    }
}
