# API Foundation

## Цель

На этом этапе добавлен базовый JSON API-слой.

Токены и JWT здесь не реализуются. Они будут добавлены отдельными этапами.

## Роуты

| Метод | URL | Назначение |
|---|---|---|
| GET | `/api/health` | Проверка API |
| GET | `/api/me` | Текущий пользователь |
| GET | `/api/companies` | Компании текущего пользователя |
| GET | `/api/companies/{slug}` | Информация о компании |

## Формат успешного ответа

```json
{
  "success": true,
  "data": {},
  "meta": {}
}
```

## Формат ошибки

```json
{
  "success": false,
  "error": {
    "code": "unauthorized",
    "message": "Требуется авторизация.",
    "details": {}
  }
}
```

## Почему не отдаём Entity напрямую

Entity не является API-контрактом.

Причины:

- в Entity могут быть приватные поля;
- связи могут привести к циклической сериализации;
- структура БД не должна диктовать внешний API;
- API-контракт должен меняться осознанно.

Поэтому используются view-фабрики:

```text
src/Api/View/UserApiViewFactory.php
src/Api/View/CompanyApiViewFactory.php
```

## Почему `/api` пока PUBLIC_ACCESS

На этом этапе ещё нет API-токенов.

Если закрыть `/api` через `ROLE_USER`, Symfony web firewall может вернуть HTML-редирект на `/login`.

Для API нам нужен JSON-ответ:

```json
{
  "success": false,
  "error": {
    "code": "unauthorized",
    "message": "Требуется авторизация."
  }
}
```

Поэтому временно:

```yaml
- { path: ^/api, roles: PUBLIC_ACCESS }
```

А контроллеры сами возвращают `401`.

На следующем этапе будет отдельный stateless API firewall и Bearer Token.

## Проверка

```bash
curl -i http://localhost:8888/api/health
curl -i http://localhost:8888/api/me
```

Для проверки авторизованных endpoint'ов можно войти через браузер:

```text
http://localhost:8888/login
```

После входа открыть:

```text
http://localhost:8888/api/me
http://localhost:8888/api/companies
http://localhost:8888/api/companies/acme-logistics
```
