<?php

namespace App\Controller;

use App\Dto\RegisterUserDto;
use App\Form\RegistrationFormType;
use App\Service\UserRegistrationService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function __invoke(
        Request                 $request,
        UserRegistrationService $userRegistrationService,
    ): Response
    {
        $dto = new RegisterUserDto();

        $form = $this->createForm(RegistrationFormType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $userRegistrationService->register($dto);
            } catch (UniqueConstraintViolationException) {
                $form->get('email')->addError(new FormError(
                    'Пользователь с таким email уже существует.'
                ));

                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form,
                ]);
            }

            $this->addFlash('success', 'Аккаунт создан. Теперь можно войти.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
