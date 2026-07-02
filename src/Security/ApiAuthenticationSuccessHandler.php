<?php

namespace App\Security;

use App\Entity\Security\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Returns the authenticated user serialized with the user:self group.
 */
class ApiAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        /** @var User $user */
        $user = $token->getUser();

        return new JsonResponse(
            $this->serializer->serialize($user, 'json', ['groups' => ['user:read', 'user:self']]),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
