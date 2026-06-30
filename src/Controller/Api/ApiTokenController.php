<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\ApiResponder;
use App\Api\View\ApiTokenViewFactory;
use App\Entity\ApiToken;
use App\Entity\User;
use App\Repository\ApiTokenRepository;
use App\Security\ApiToken\ApiTokenIssuer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/tokens')]
final class ApiTokenController
{
    #[Route('', name: 'api_tokens_index', methods: ['GET'])]
    public function index(
        #[CurrentUser] User $user,
        ApiTokenRepository  $apiTokenRepository,
        ApiTokenViewFactory $apiTokenViewFactory,
        ApiResponder        $api,
    ): jsonResponse
    {
        $tokens = $apiTokenRepository->findForUser($user);

        return $api->success([
            'items' => array_map(
                static fn(ApiToken $token): array => $apiTokenViewFactory->create($token),
                $tokens,
            ),
        ], meta: [
            'count' => count($tokens)
        ]);
    }

    #[Route('', name: 'api_tokens_create', methods: ['POST'])]
    public function create(
        #[CurrentUser] User $user,
        Request             $request,
        ApiTokenIssuer      $apiTokenIssuer,
        ApiTokenViewFactory $apiTokenViewFactory,
        ApiResponder        $api,
    ): JsonResponse
    {
        try {
            $payload = $request->toArray();
        } catch (\JsonException) {
            return $api->error(
                message: 'Некорректный JSON.',
                code: 'invalid_json',
            );
        }

        $name = trim((string) ($payload['name'] ?? ''));

        if ($name === '') {
            return $api->error(
                message: 'Название токена обязательно.',
                code: 'validation_error',
                status: JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                details: [
                    'name' => 'Поле name обязательно.',
                ],
            );
        }

        if (mb_strlen($name) > 120) {
            return $api->error(
                message: 'Название токена слишком длинное.',
                code: 'validation_error',
                status: JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                details: [
                    'name' => 'Максимальная длина — 120 символов.',
                ],
            );
        }

        $expiresAt = null;

        if (!empty($payload['expires_at'])) {
            try {
                $expiresAt = new \DateTimeImmutable((string) $payload['expires_at']);
            } catch (\Exception) {
                return $api->error(
                    message: 'Некорректная дата expires_at.',
                    code: 'validation_error',
                    status: JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                    details: [
                        'expires_at' => 'Передайте дату в формате ISO 8601 или строку, которую понимает DateTimeImmutable.',
                    ],
                );
            }
        }

        $issuedToken = $apiTokenIssuer->issue($user, $name, $expiresAt);

        return $api->success([
            'token' => $apiTokenViewFactory->create($issuedToken->apiToken),
            'plain_token' => $issuedToken->plainToken,
            'warning' => 'Сохраните plain_token сейчас. Повторно получить его будет невозможно.',
        ], status: JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}/revoke', name: 'api_token_revoke', methods: ['POST'])]
    public function revoke(
        int $id,
        #[CurrentUser] User $user,
        ApiTokenRepository $apiTokenRepository,
        EntityManagerInterface $entityManager,
        ApiTokenViewFactory $apiTokenViewFactory,
        ApiResponder $api,
    ): JsonResponse {
        $token = $apiTokenRepository->findOneForUser($id, $user);

        if ($token === null) {
            return $api->notFound('API-токен не найден.');
        }

        $token->revoke();
        $entityManager->flush();

        return $api->success([
            'token' => $apiTokenViewFactory->create($token),
        ]);
    }
}
