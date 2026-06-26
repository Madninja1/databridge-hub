<?php
declare(strict_types=1);

namespace App\Form;

use App\Dto\RegisterUserDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Пароль'
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Имя'
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Фамилия',
                'required' => false
            ])
            ->add('companyName', TextType::class, [
                'label' => 'Компания'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
           'data_class' => RegisterUserDto::class,
        ]);
    }
}
