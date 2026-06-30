<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\ApiResponder;
use App\Api\View\CompanyApiViewFactory;
use App\Entity\Company;
use App\Entity\User;
use App\Repository\CompanyRepository;
use App\Security\CompanyPermission;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/companies')]
final class ApiCompanyController extends AbstractController
{
    #[Route('', name: 'api_company_index', methods: ['GET'])]
    public function index(
        #[CurrentUser] ?User  $user,
        CompanyRepository     $companyRepository,
        CompanyApiViewFactory $companyApiViewFactory,
        ApiResponder          $api,
    ): JsonResponse
    {
        if ($user === null) {
            return $api->unauthorized();
        }

        $companies = $companyRepository->findForUser($user);

        return $api->success([
            'items' => array_map(
                static fn (Company $company) => $companyApiViewFactory->create($company),
                $companies,
            ),
        ], meta: [
            'count' => count($companies),
        ]);
    }

    #[Route('/{slug}', name: 'api_company_show', methods: ['GET'])]
    public function show(
        string                $slug,
        #[CurrentUser] ?User  $user,
        CompanyRepository     $companyRepository,
        CompanyApiViewFactory $companyApiViewFactory,
        ApiResponder          $api,
    ): JsonResponse
    {
        if ($user === null) {
            return $api->unauthorized();
        }

        $company = $companyRepository->findOneActiveBySlug($slug);

        if ($company === null) {
            return $api->notFound('Компания не найдена.');
        }

        if (!$this->isGranted(CompanyPermission::VIEW, $company)) {
            return $api->forbidden('Нет доступа к этой компании.');
        }

        return $api->success([
            'company' => $companyApiViewFactory->create($company),
            'permissions' => [
                'can_view' => $this->isGranted(CompanyPermission::VIEW, $company),
                'can_manage' => $this->isGranted(CompanyPermission::MANAGE, $company),
                'can_manage_members' => $this->isGranted(CompanyPermission::MEMBERS, $company),
                'can_manage_api_tokens' => $this->isGranted(CompanyPermission::API_TOKENS, $company),
            ],
        ]);
    }
}
