<?php
declare(strict_types=1);

namespace App\Api\View;

use App\Entity\User;

final class UserApiViewFactory
{
    public function create(User $user): array
    {
        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'roles' => $user->getRoles(),
            'is_verified' => $user->isVerified(),
            'created_at' => $user->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'updated_at' => $user->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        ];
    }
}
