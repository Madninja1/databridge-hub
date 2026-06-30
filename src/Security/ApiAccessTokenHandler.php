<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Repository\ApiTokenRepository;
use App\Security\ApiToken\ApiTokenHasher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

final class ApiAccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private readonly ApiTokenRepository     $apiTokenRepository,
        private readonly ApiTokenHasher         $apiTokenHasher,
        private readonly EntityManagerInterface $em,
    )
    {

    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        $tokenHash = $this->apiTokenHasher->hashPlainToken($accessToken);
        $apiToken = $this->apiTokenRepository->findOneByTokenHash($tokenHash);

        if ($apiToken === null || !$apiToken->isValid()) {
            throw new BadCredentialsException('Invalid API token.');
        }

        $user = $apiToken->getUser();

        if (!$user instanceof User) {
            throw new BadCredentialsException('Invalid API token user.');
        }

        $apiToken->markUsed();
        $this->em->flush();

        return new UserBadge(($user->getEmail()));
    }
}
