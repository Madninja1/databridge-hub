<?php
declare(strict_types=1);

namespace App\Security\ApiToken;

use App\Entity\ApiToken;

final class IssuedApiToken
{
    public function __construct(
        public readonly ApiToken $apiToken,
        public readonly string   $plainToken,
    )
    {

    }
}
