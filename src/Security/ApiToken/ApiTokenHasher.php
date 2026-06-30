<?php
declare(strict_types=1);

namespace App\Security\ApiToken;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class ApiTokenHasher
{
    public function __construct(
        #[Autowire('%env(API_TOKEN_HASH_SECRET)%')] private readonly string $secret
    )
    {
    }

    public function generatePlainToken(): string
    {
        $random = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');

        return 'dtb_' . $random;
    }

    public function hashPlainToken(string $plainToken): string
    {
        return hash_hmac('sha256', $plainToken, $this->secret);
    }

    public function getPrefix(string $plainToken): string
    {
        return mb_substr($plainToken, 0, 16);
    }
}
