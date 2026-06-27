<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\Company;
use App\Entity\User;
use App\Enum\CompanyMembershipRole;
use App\Repository\CompanyMembershipRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class CompanyVoter extends Voter
{
    public function __construct(
        private readonly CompanyMembershipRepository $membershipRepository
    )
    {

    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [
                CompanyPermission::VIEW,
                CompanyPermission::MANAGE,
                CompanyPermission::MEMBERS,
                CompanyPermission::API_TOKENS,
            ], true) && $subject instanceof Company;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if (!$subject instanceof Company) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        $membership = $this->membershipRepository->findOneForUserAndCompany(
            $user,
            $subject
        );

        if ($membership === null) {
            return false;
        }

        return match ($attribute) {
            CompanyPermission::VIEW => true,

            CompanyPermission::MANAGE,
            CompanyPermission::MEMBERS,
            CompanyPermission::API_TOKENS => in_array($membership->getRole(), [
                CompanyMembershipRole::Owner,
                CompanyMembershipRole::Admin,
            ], true),

            default => false,
        };
    }
}
