<?php

namespace App\Repository;

use App\Entity\ApiToken;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ApiToken>
 */
class ApiTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiToken::class);
    }

    public function save(ApiToken $apiToken, bool $flush = true): void
    {
        $this->getEntityManager()->persist($apiToken);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ApiToken $apiToken, bool $flush = true): void
    {
        $this->getEntityManager()->remove($apiToken);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByTokenHash(string $tokenHash): ?ApiToken
    {
        return $this->findOneBy([
            'tokenHash' => $tokenHash,
        ]);
    }

    public function findForUser(User $user): array
    {
        return $this->createQueryBuilder('token')
            ->andWhere('token.user = :user')
            ->setParameter('user', $user)
            ->orderBy('token.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findOneForUser(int $id, User $user): ?ApiToken
    {
        return $this->createQueryBuilder('token')
            ->andWhere('token.id = :id')
            ->andWhere('token.user = :user')
            ->setParameter('id', $id)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
