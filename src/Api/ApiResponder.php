<?php

declare(strict_types=1);

namespace App\Api;

use Symfony\Component\HttpFoundation\JsonResponse;

final class ApiResponder
{
    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $meta
     */
    public function success(
        array $data = [],
        int $status = JsonResponse::HTTP_OK,
        array $meta = [],
    ): JsonResponse {
        return new JsonResponse([
            'success' => true,
            'data' => $data !== [] ? $data : new \stdClass(),
            'meta' => $meta!== [] ? $data : new \stdClass(),
        ], $status);
    }

    /**
     * @param array<string, mixed> $details
     */
    public function error(
        string $message,
        string $code = 'error',
        int $status = JsonResponse::HTTP_BAD_REQUEST,
        array $details = [],
    ): JsonResponse {
        return new JsonResponse([
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
                'details' => $details !== [] ? $details : new \stdClass(),
            ],
        ], $status);
    }

    public function unauthorized(string $message = 'Требуется авторизация.'): JsonResponse
    {
        return $this->error(
            message: $message,
            code: 'unauthorized',
            status: JsonResponse::HTTP_UNAUTHORIZED,
        );
    }

    public function forbidden(string $message = 'Доступ запрещён.'): JsonResponse
    {
        return $this->error(
            message: $message,
            code: 'forbidden',
            status: JsonResponse::HTTP_FORBIDDEN,
        );
    }

    public function notFound(string $message = 'Ресурс не найден.'): JsonResponse
    {
        return $this->error(
            message: $message,
            code: 'not_found',
            status: JsonResponse::HTTP_NOT_FOUND,
        );
    }
}
