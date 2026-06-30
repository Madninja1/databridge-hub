<?php

namespace App\Entity;

use App\Repository\ApiTokenRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApiTokenRepository::class)]
#[ORM\Table(name: 'api_token')]
#[Orm\UniqueConstraint(name: 'UNIQ_API_TOKEN_HASH', columns: ['token_hash'])]
#[ORM\Index(name: 'IDX_API_TOKEN_USER', columns: ['user_id'])]
#[ORM\Index(name: 'IDX_API_TOKEN_PREFIX', columns: ['token_prefix'])]
class ApiToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    private ?User $user = null;

    #[ORM\Column(length: 120)]
    private ?string $name = '';

    #[ORM\Column(length: 16)]
    private ?string $tokenPrefix = '';

    #[ORM\Column(length: 64)]
    private ?string $tokenHash = '';

    #[ORM\Column]
    private array $scopes = [];

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE,)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $expiresAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastUsedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $revokedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getTokenPrefix(): ?string
    {
        return $this->tokenPrefix;
    }

    public function setTokenPrefix(string $tokenPrefix): static
    {
        $this->tokenPrefix = $tokenPrefix;

        return $this;
    }

    public function getTokenHash(): ?string
    {
        return $this->tokenHash;
    }

    public function setTokenHash(string $tokenHash): static
    {
        $this->tokenHash = $tokenHash;

        return $this;
    }

    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function setScopes(array $scopes): static
    {
        $this->scopes = $scopes;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeImmutable $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getLastUsedAt(): ?\DateTimeImmutable
    {
        return $this->lastUsedAt;
    }

    public function markUsed(): static
    {
        $this->lastUsedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getRevokedAt(): ?\DateTimeImmutable
    {
        return $this->revokedAt;
    }

    public function revoke(): static
    {
        if ($this->revokedAt === null) {
            $this->revokedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function isRevoked(): bool
    {
        return $this->revokedAt !== null;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt !== null && $this->expiresAt <= new \DateTimeImmutable();
    }

    public function isValid(): bool
    {
        return !$this->isRevoked() && !$this->isExpired();
    }
}
