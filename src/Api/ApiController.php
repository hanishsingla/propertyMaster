<?php

namespace App\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

/**
 * Shared helpers for JSON API controllers: body decoding, list envelopes,
 * and validation that surfaces as RFC7807 422 responses (via ApiExceptionSubscriber).
 */
abstract class ApiController extends AbstractController
{
    /**
     * Decode a JSON request body to an associative array.
     *
     * @return array<string, mixed>
     */
    protected function decode(Request $request): array
    {
        if ('' === $request->getContent()) {
            return [];
        }

        try {
            $data = $request->toArray();
        } catch (\JsonException|\Symfony\Component\HttpFoundation\Exception\JsonException) {
            throw new BadRequestHttpException('Malformed JSON body.');
        }

        return $data;
    }

    /**
     * Standard list envelope: { data, meta: { page, perPage, total, totalPages } }.
     *
     * @param array<int, mixed> $data
     */
    protected function envelope(array $data, int $page, int $perPage, int $total): JsonResponse
    {
        return new JsonResponse([
            'data' => $data,
            'meta' => [
                'page' => $page,
                'perPage' => $perPage,
                'total' => $total,
                'totalPages' => $perPage > 0 ? (int) ceil($total / $perPage) : 0,
            ],
        ]);
    }

    /**
     * Throw a 422 if the violation list is non-empty.
     */
    protected function assertValid(ConstraintViolationListInterface $violations, mixed $value = null): void
    {
        if (count($violations) > 0) {
            throw new ValidationFailedException($value, $violations);
        }
    }

    protected function noContent(): JsonResponse
    {
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
