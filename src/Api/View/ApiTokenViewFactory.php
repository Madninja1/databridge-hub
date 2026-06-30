<?php
declare(strict_types=1);

namespace App\Api\View;

use App\Entity\ApiToken;

final class ApiTokenViewFactory
{
    public function create(ApiToken $apiToken): array
    {
        return [
            'id' => $apiToken->getId(),
            'name' => $apiToken->getName(),
            'prefix' => $apiToken->getTokenPrefix(),
            'scopes' => $apiToken->getScopes(),
            'is_valid' => $apiToken->isValid(),
            'is_revoked' => $apiToken->isRevoked(),
            'is_expired' => $apiToken->isExpired(),
            'created_at' => $apiToken->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'expires_at' => $apiToken->getExpiresAt()?->format(\DateTimeInterface::ATOM),
            'last_used_at' => $apiToken->getLastUsedAt()?->format(\DateTimeInterface::ATOM),
            'revoked_at' => $apiToken->getRevokedAt()?->format(\DateTimeInterface::ATOM),
        ];
    }
}
