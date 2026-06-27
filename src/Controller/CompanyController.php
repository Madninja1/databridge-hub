<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CompanyRepository;
use App\Security\CompanyPermission;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/companies')]
final class CompanyController extends AbstractController
{
    #[Route('', name: 'app_company_index', methods: ['GET'])]
    public function index(CompanyRepository $companyRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('company/index.html.twig', [
            'companies' => $companyRepository->findForUser($user),
        ]);
    }

    // todo Убрать ручной поиск по slug, для обучения и явной работы с repo и меньшей магии, сделал текущую реализацию
    #[Route('/{slug}', name: 'app_company_show', methods: ['GET'])]
    public function show(string $slug, CompanyRepository $companyRepository): Response
    {
        $company = $companyRepository->findOneActiveBySlug($slug);

        if ($company === null) {
            throw $this->createNotFoundException('Компания не найдена.');
        }

        $this->denyAccessUnlessGranted(CompanyPermission::VIEW, $company);

        return $this->render('company/show.html.twig', [
            'company' => $company,
        ]);
    }
}
