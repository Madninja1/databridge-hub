<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\CompanyMembership;
use App\Entity\User;
use App\Enum\CompanyMembershipRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    )
    {

    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@databridge.local')
            ->setFirstName('Admin')
            ->setLastName('User')
            ->setRoles(['ROLE_ADMIN'])
            ->setIsVerified(true);

        $admin->setPassword($this->userPasswordHasher->hashPassword($admin, 'password'));

        $company = new Company();
        $company->setName('Acme Logistics')
            ->setSlug('acme-logistics')
            ->setIsActive(true);

        $membership = new CompanyMembership();
        $membership->setCompany($company)
            ->setUser($admin)
            ->setRole(CompanyMembershipRole::Owner);

        $manager->persist($admin);
        $manager->persist($company);
        $manager->persist($membership);

        $manager->flush();
    }
}
