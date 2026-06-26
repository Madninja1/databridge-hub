<?php
declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class RegisterUserDto
{
    #[Assert\NotBlank(message: 'Email обязателен.')]
    #[Assert\Email(message: 'Некорректный email.')]
    public string $email = '';

    #[Assert\NotBlank(message: 'Пароль обязателен')]
    #[Assert\Length(
        min: 8,
        max: 4096,
        minMessage: 'Пароль должен быть не короче {{ limit}} символов.')
    ]
    public string $plainPassword = '';

    #[Assert\NotBlank(message: 'Имя обязательно.')]
    #[Assert\Length(max: 100)]
    public string $firstName = '';

    #[Assert\Length(max: 100)]
    public string $lastName = '';

    #[Assert\NotBlank(message: 'Название компании обязательно.')]
    #[Assert\Length(max: 255)]
    public string $companyName = '';
}
