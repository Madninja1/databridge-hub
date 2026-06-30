<?php
declare(strict_types=1);

namespace App\Api\View;

use App\Entity\Company;

final class CompanyApiViewFactory
{
    public function create(Company $company): array
    {
        return [
            'id' => $company->getId(),
            'name' => $company->getName(),
            'slug' => $company->getSlug(),
            'is_active' => $company->isActive(),
            'created_at' => $company->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'updated_at' => $company->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        ];
    }
}
