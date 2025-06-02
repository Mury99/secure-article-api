<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Listener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

#[AsEventListener(event: 'kernel.exception', method: 'onKernelException')]
class AccessDeniedExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof AccessDeniedHttpException) {
            return;
        }

        $response = new JsonResponse([
            'message' => $exception->getMessage(),
        ], Response::HTTP_FORBIDDEN);

        $event->setResponse($response);
    }
}
