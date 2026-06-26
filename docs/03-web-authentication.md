# Web Authentication

## Цель

На этом этапе добавлена web-аутентификация через Symfony Security:

- регистрация;
- вход через форму;
- выход;
- защищённая страница `/dashboard`.

## Роуты

| Роут | Назначение |
|---|---|
| `/register` | Регистрация пользователя и компании |
| `/login` | Форма входа |
| `/logout` | Завершение сессии |
| `/dashboard` | Защищённая страница |

## Как работает login

Symfony `form_login` перехватывает POST-запрос на `/login`.

Форма отправляет:

- `_username`
- `_password`
- `_csrf_token`

Symfony:

1. находит пользователя через user provider;
2. проверяет пароль через password hasher;
3. создаёт authenticated session;
4. перенаправляет пользователя на `/dashboard`.

## Почему регистрация создаёт компанию

В DataBridge Hub почти все будущие данные принадлежат компании:

- импорты;
- экспорты;
- API-токены;
- пользователи;
- права доступа.

Поэтому при регистрации сразу создаётся:

- User;
- Company;
- CompanyMembership с ролью `owner`.

## Почему используем DTO

Форма регистрации содержит `plainPassword` и `companyName`.

Эти поля не должны напрямую жить в `User`:

- `plainPassword` нельзя хранить в БД;
- `companyName` относится к Company, а не к User.

Поэтому используется `RegisterUserDto`.

## Проверка

Открыть:

```text
http://localhost:8888/register
