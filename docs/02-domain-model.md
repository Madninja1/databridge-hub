# Domain Model: User, Company, CompanyMembership

## Цель

На этом этапе мы добавили базовую модель приложения:

- User
- Company
- CompanyMembership

Эта модель нужна для авторизации, API-токенов, импортов, экспортов и разграничения доступа между компаниями.

## Почему не ManyToMany

Между User и Company можно было бы сделать ManyToMany, но нам нужно хранить дополнительные данные о связи:

- роль пользователя внутри компании;
- дату добавления;
- в будущем статус приглашения.

Поэтому используется отдельная сущность CompanyMembership.

## Роли

Глобальные роли Symfony хранятся в User.roles:

- ROLE_USER
- ROLE_ADMIN

Роли внутри компании хранятся в CompanyMembership.role:

- owner
- admin
- member

Это разные уровни прав.

## Тестовый пользователь

```text
email: admin@databridge.local
password: password
