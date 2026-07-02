<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

/**
 * Unauthenticated /api requests get a 401 JSON body (RFC7807-ish);
 * everything else (e.g. /admin) redirects to the SPA login route.
 */
class ApiEntryPoint implements AuthenticationEntryPointInterface
{
    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        if (str_starts_with($request->getPathInfo(), '/api')) {
            return new JsonResponse([
                'error' => [
                    'code' => 'unauthenticated',
                    'message' => 'Authentication required.',
                ],
            ], Response::HTTP_UNAUTHORIZED);
        }

        return new RedirectResponse('/login');
    }
}
