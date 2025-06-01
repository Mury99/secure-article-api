<?php

declare(strict_types=1);

namespace App\Domain\User\Repository;

use App\Domain\User\Entity\User;

interface UserRepositoryInterface
{
    public function findById(string $id): ?User;

    /**
     * @return list<User>
     */
    public function findAll(): array;
}
