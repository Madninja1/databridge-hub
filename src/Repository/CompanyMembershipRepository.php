<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\CompanyMembership;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CompanyMembership>
 */
class CompanyMembershipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyMembership::class);
    }

    public function save(CompanyMembership $membership, bool $flush = true): void
    {
        $this->getEntityManager()->persist($membership);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CompanyMembership $membership, bool $flush = true): void
    {
        $this->getEntityManager()->remove($membership);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneForUserAndCompany(User $user, Company $company): ?CompanyMembership
    {
        return $this->findOneBy([
            'user' => $user,
            'company' => $company
        ]);
    }
}
