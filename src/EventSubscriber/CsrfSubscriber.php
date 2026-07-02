<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Double-submit CSRF guard for mutating /api requests (contract §1/§6).
 * The token is issued by GET /api/csrf and echoed back in the X-CSRF-Token
 * header. Login/logout are exempt (json_login has no prior token; logout is
 * safe as it only ends a session).
 */
class CsrfSubscriber implements EventSubscriberInterface
{
    private const TOKEN_ID = 'api';
    private const SAFE_METHODS = ['GET', 'HEAD', 'OPTIONS'];
    private const EXEMPT_PATHS = ['/api/auth/login', '/api/auth/logout'];

    public function __construct(private readonly CsrfTokenManagerInterface $csrfTokenManager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onRequest', 8]];
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $path = $request->getPathInfo();

        if (!str_starts_with($path, '/api')) {
            return;
        }
        if (in_array($request->getMethod(), self::SAFE_METHODS, true)) {
            return;
        }
        if (in_array($path, self::EXEMPT_PATHS, true)) {
            return;
        }

        $submitted = $request->headers->get('X-CSRF-Token', '');
        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken(self::TOKEN_ID, $submitted))) {
            $event->setResponse(new JsonResponse([
                'error' => [
                    'code' => 'csrf_invalid',
                    'message' => 'Invalid or missing CSRF token.',
                ],
            ], Response::HTTP_FORBIDDEN));
        }
    }

    public static function tokenId(): string
    {
        return self::TOKEN_ID;
    }
}
