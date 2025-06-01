<?php

declare(strict_types=1);

namespace App\Domain\User\Factory;

use App\Application\User\Command\UserCreateCommand;
use App\Domain\User\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserFactory
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function createFromCommand(UserCreateCommand $command): User
    {
        $user = new User();
        $user->setEmail($command->username);
        $user->setName($command->name);
        $user->setRolesFromEnum($command->roles);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $command->password);
        $user->setPassword($hashedPassword);

        return $user;
    }
}
