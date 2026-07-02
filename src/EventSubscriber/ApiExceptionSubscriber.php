<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationFailedException;

/**
 * Renders all /api exceptions as RFC7807-ish JSON (contract §6).
 */
class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly KernelInterface $kernel)
    {
    }

    public static function getSubscribedEvents(): array
    {
        // Priority above the default (which would render an HTML error page).
        return [KernelEvents::EXCEPTION => ['onException', 10]];
    }

    public function onException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();
        if (!str_starts_with($request->getPathInfo(), '/api')) {
            return;
        }

        $throwable = $event->getThrowable();

        // Let Symfony's security listener convert access/authentication errors
        // (anonymous -> 401 via ApiEntryPoint, authenticated -> 403). These are
        // NOT HttpExceptions, so we'd otherwise mislabel them as 500.
        if ($throwable instanceof \Symfony\Component\Security\Core\Exception\AccessDeniedException
            || $throwable instanceof \Symfony\Component\Security\Core\Exception\AuthenticationException) {
            return;
        }

        // Validation errors -> 422 with per-field violations.
        $validationException = $this->findValidationException($throwable);
        if ($validationException instanceof ValidationFailedException) {
            $violations = [];
            foreach ($validationException->getViolations() as $violation) {
                $violations[] = [
                    'field' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
            }

            $event->setResponse(new JsonResponse([
                'error' => [
                    'code' => 'validation_failed',
                    'message' => 'The submitted data is invalid.',
                    'violations' => $violations,
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY));

            return;
        }

        $status = $throwable instanceof HttpExceptionInterface
            ? $throwable->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;

        $code = match ($status) {
            Response::HTTP_UNAUTHORIZED => 'unauthenticated',
            Response::HTTP_FORBIDDEN => 'forbidden',
            Response::HTTP_NOT_FOUND => 'not_found',
            Response::HTTP_CONFLICT => 'conflict',
            Response::HTTP_TOO_MANY_REQUESTS => 'rate_limited',
            default => 'error',
        };

        // Hide internal messages in prod for 5xx.
        $debug = $this->kernel->isDebug();
        $message = ($status >= 500 && !$debug)
            ? 'An unexpected error occurred.'
            : $throwable->getMessage();

        $event->setResponse(new JsonResponse([
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ], $status));
    }

    private function findValidationException(\Throwable $throwable): ?ValidationFailedException
    {
        while (null !== $throwable) {
            if ($throwable instanceof ValidationFailedException) {
                return $throwable;
            }
            $throwable = $throwable->getPrevious();
        }

        return null;
    }
}
