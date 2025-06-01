<?php

declare(strict_types=1);

namespace App\Domain\User\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserNotFoundException extends NotFoundHttpException
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('User with ID - %s not found.', $id));
    }
}
