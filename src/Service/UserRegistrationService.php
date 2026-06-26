<?php
declare(strict_types=1);

namespace App\Service;

use App\Dto\RegisterUserDto;
use App\Entity\Company;
use App\Entity\CompanyMembership;
use App\Entity\User;
use App\Enum\CompanyMembershipRole;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserRegistrationService
{
    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly CompanyRepository           $companyRepository,
        private readonly Slugger                     $slugger,
    )
    {

    }

    public function register(RegisterUserDto $dto): User
    {
        $user = new User();
        $user
            ->setEmail($dto->email)
            ->setFirstName($dto->firstName)
            ->setLastName($dto->lastName)
            ->setRoles(['ROLE_USER'])
            ->setIsVerified(true);

        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $dto->plainPassword)
        );

        $company = new Company();
        $company
            ->setName($dto->companyName)
            ->setSlug($this->generateUniqueCompanySlug($dto->companyName))
            ->setIsActive(true);

        $membership = new CompanyMembership();
        $membership
            ->setUser($user)
            ->setCompany($company)
            ->setRole(CompanyMembershipRole::Owner);

        $this->entityManager->persist($user);
        $this->entityManager->persist($company);
        $this->entityManager->persist($membership);
        $this->entityManager->flush();

        return $user;
    }

    private function generateUniqueCompanySlug(string $companyName): string
    {
        $baseSlug = $this->slugger->slugify($companyName);
        $slug = $baseSlug;
        $counter = 2;

        while ($this->companyRepository->existsBySlug($slug)) {
            $slug = sprintf('%s-%d', $baseSlug, $counter);
            ++$counter;
        }

        return $slug;
    }
}
