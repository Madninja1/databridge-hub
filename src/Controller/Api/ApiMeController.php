<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\ApiResponder;
use App\Api\View\UserApiViewFactory;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class ApiMeController
{
    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function __invoke(
        #[CurrentUser] ?User $user,
        ApiResponder $api,
        UserApiViewFactory $userApiViewFactory
    ): JsonResponse
    {
        if ($user === null) {
            return $api->unauthorized();
        }

        return $api->success([
            'user' => $userApiViewFactory->create($user),
        ]);
    }
}
