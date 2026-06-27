<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Company>
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    public function save(Company $company, bool $flush = true): void
    {
        $this->getEntityManager()->persist($company);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Company $company, bool $flush = true): void
    {
        $this->getEntityManager()->remove($company);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function existsBySlug(string $slug): bool
    {
        return $this->createQueryBuilder('company')
            ->select('count(company.id)')
            ->andWhere('company.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    public function findForUser(User $user): array
    {
        return $this->createQueryBuilder('company')
            ->innerJoin('company.memberships', 'membership')
            ->andWhere('membership.user = :user')
            ->andWhere('company.isActive = :isActive')
            ->setParameter('user', $user)
            ->setParameter('isActive', true)
            ->orderBy('company.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneActiveBySlug(string $slug): ?Company
    {
        return $this->createQueryBuilder('company')
            ->andWhere('company.slug = :slug')
            ->andWhere('company.isActive = :isActive')
            ->setParameter('slug', $slug)
            ->setParameter('isActive', true)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
