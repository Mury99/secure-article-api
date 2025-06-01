<?php

declare(strict_types=1);

namespace App\Domain\User\Exception;

use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class EmailAlreadyTakenException extends ConflictHttpException
{
    public function __construct(string $email, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf('The email "%s" is already in use.', $email), $previous);
    }
}
