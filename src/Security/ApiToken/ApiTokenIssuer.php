<?php
declare(strict_types=1);

namespace App\Security\ApiToken;

use App\Entity\ApiToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class ApiTokenIssuer
{
    public function __construct(
        private readonly ApiTokenHasher         $hasher,
        private readonly EntityManagerInterface $em,
    )
    {

    }

    public function issue(
        User                $user,
        string              $name,
        ?\DateTimeImmutable $expiresAt = null,
    ): IssuedApiToken
    {
        $plainToken = $this->hasher->generatePlainToken();

        $apiToken = new ApiToken();
        $apiToken
            ->setUser($user)
            ->setName($name)
            ->setTokenPrefix($this->hasher->getPrefix($plainToken))
            ->setTokenHash($this->hasher->hashPlainToken($plainToken))
            ->setScopes(['api'])
            ->setExpiresAt($expiresAt);

        $this->em->persist($apiToken);
        $this->em->flush();

        return new IssuedApiToken($apiToken, $plainToken);
    }
}
