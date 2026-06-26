<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\CompanyMembershipRole;
use App\Repository\CompanyMembershipRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyMembershipRepository::class)]
#[ORM\Table(name: 'company_membership')]
#[ORM\UniqueConstraint(
    name: 'UNIQ_COMPANY_MEMBERSHIP_USER_COMPANY',
    columns: ['user_id', 'company_id']
)]
class CompanyMembership
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'memberships')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'memberships')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Company $company = null;

    #[ORM\Column(type: 'string', enumType: CompanyMembershipRole::class)]
    private CompanyMembershipRole $role = CompanyMembershipRole::Member;

    #[ORM\Column(
        name: 'created_at',
        type: Types::DATETIMETZ_IMMUTABLE,
        insertable: false,
        updatable: false,
        options: ['default' => 'CURRENT_TIMESTAMP']
    )]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(
        name: 'updated_at',
        type: Types::DATETIMETZ_IMMUTABLE,
        insertable: false,
        updatable: false,
        options: ['default' => 'CURRENT_TIMESTAMP']
    )]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getRole(): CompanyMembershipRole
    {
        return $this->role;
    }

    public function setRole(CompanyMembershipRole $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function isOwner(): bool
    {
        return CompanyMembershipRole::Owner === $this->role;
    }

    public function isAdmin(): bool
    {
        return CompanyMembershipRole::Admin === $this->role;
    }

    public function isMember(): bool
    {
        return CompanyMembershipRole::Member === $this->role;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
