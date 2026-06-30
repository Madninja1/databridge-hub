<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\ApiResponder;
use App\Api\View\ApiTokenViewFactory;
use App\Api\View\UserApiViewFactory;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\ApiToken\ApiTokenIssuer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class ApiAuthTokenController
{
    #[Route('api/auth/token', name: 'api_auth_token_create', methods: ['POST'])]
    public function __invoke(
        Request                     $request,
        UserRepository              $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        ApiTokenIssuer              $apiTokenIssuer,
        ApiTokenViewFactory         $apiTokenViewFactory,
        UserApiViewFactory          $userApiViewFactory,
        ApiResponder                $api,
    )
    {
        try {
            $payload = $request->toArray();
        } catch (\JsonException) {
            return $api->error(
                message: 'Некорректный JSON.',
                code: 'invalid_json',
                status: JsonResponse::HTTP_BAD_REQUEST,
            );
        }

        $email = mb_strtolower(trim((string)($payload['email'] ?? '')));
        $password = (string)($payload['password'] ?? '');
        $name = trim((string)($payload['name'] ?? 'API login token'));

        if ($email === '') {
            return $api->error(
                message: 'Email обязателен.',
                code: 'validation_error',
                status: JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                details: [
                    'email' => 'Поле email обязательно.'
                ],
            );
        }

        if ($password == '') {
            return $api->error(
                message: 'Пароль обязателен.',
                code: 'validation_error',
                status: JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                details: [
                    'password' => 'Поле password обязательно.'
                ],
            );
        }

        if ($name === '') {
            $name = 'API login token';
        }

        if (mb_strlen($name) > 120) {
            return $api->error(
                message: 'Название токена слишком длинное.',
                code: 'validation_error',
                status: JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                details: [
                    'name' => 'Максимальная длина - 120 символов.'
                ],
            );
        }

        $user = $userRepository->findOneBy([
            'email' => $email,
        ]);

        if (!$user instanceof User || !$passwordHasher->isPasswordValid($user, $password)) {
            return $api->error(
                message: 'Неверный email или пароль',
                code: 'invalid_credentials',
                status: JsonResponse::HTTP_UNAUTHORIZED,
            );
        }

        if (!$user->isVerified()) {
            return $api->error(
                message: 'Пользователь не найден.',
                code: 'user_not_verified',
                status: JsonResponse::HTTP_FORBIDDEN,
            );
        }

        $expiresAt = new \DateTimeImmutable('+ 30 days');

        if (array_key_exists('expires_at', $payload) && $payload['expires_at'] !== null) {
            try {
                $expiresAt = new \DateTimeImmutable((string)$payload['expires_at']);
            } catch (\Exception) {
                return $api->error(
                    message: 'Некорректная дата expires_at.',
                    code: 'validation_error',
                    status: JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                    details: [
                        'expires_at' => 'Передайте дату в формате ISO 8601 или строку вроде "+7 days".'
                    ],
                );
            }
        }

        $issuedToken = $apiTokenIssuer->issue(
            user: $user,
            name: $name,
            expiresAt: $expiresAt,
        );

        return $api->success([
           'user' => $userApiViewFactory->create($user),
           'token' => $apiTokenViewFactory->create($issuedToken->apiToken),
           'plain_token' => $issuedToken->plainToken,
           'token_type' => 'Bearer',
           'warning' => 'Сохраните plain_token сейчас. Повторно получить его будет невозможно.'
        ], status: JsonResponse::HTTP_CREATED);
    }
}
