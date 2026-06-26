PHP=docker compose exec php

COMPOSER_RUN=docker compose run --rm \
	-e COMPOSER_PROCESS_TIMEOUT=2000 \
	-e COMPOSER_IPRESOLVE=4 \
	php composer

COMPOSER_EXEC=docker compose exec \
	-e COMPOSER_PROCESS_TIMEOUT=2000 \
	-e COMPOSER_IPRESOLVE=4 \
	php composer

CONSOLE=$(PHP) php bin/console

build:
	docker compose build

up:
	docker compose up -d

down:
	docker compose down

restart:
	docker compose down
	docker compose up -d

ps:
	docker compose ps

logs:
	docker compose logs -f

sh:
	$(PHP) bash

create-project:
	docker compose run --rm php rm -rf tmp
	$(COMPOSER_RUN) create-project symfony/skeleton:"7.4.*" tmp --no-interaction --no-progress --prefer-dist
	docker compose run --rm php sh -lc 'cp -a tmp/. . && rm -rf tmp'
	$(COMPOSER_EXEC) config extra.symfony.require "7.4.*"

install:
	$(COMPOSER_RUN) install --no-interaction --no-progress --prefer-dist

require-base:
	$(COMPOSER_RUN) require \
		symfony/orm-pack \
		symfony/security-bundle \
		symfony/twig-bundle \
		symfony/asset \
		symfony/validator \
		symfony/serializer-pack \
		symfony/messenger \
		symfony/amqp-messenger \
		symfony/cache \
		--no-interaction \
		--no-progress \
		--prefer-dist \
		--with-all-dependencies

	$(COMPOSER_RUN) require --dev \
		symfony/maker-bundle \
		symfony/debug-bundle \
		symfony/web-profiler-bundle \
		--no-interaction \
		--no-progress \
		--prefer-dist \
		--with-all-dependencies

cc:
	$(CONSOLE) cache:clear

about:
	$(CONSOLE) about

routes:
	$(CONSOLE) debug:router

db-create:
	$(CONSOLE) doctrine:database:create --if-not-exists

validate-schema:
	$(CONSOLE) doctrine:schema:validate

# Symfony MakerBundle
require-maker:
	docker compose exec php composer require --dev symfony/maker-bundle

make-list:
	docker compose exec php php bin/console list make

make-controller:
	@if [ -z "$(name)" ]; then \
		echo "Usage: make make-controller name=HealthController"; \
		exit 1; \
	fi
	docker compose exec php php bin/console make:controller $(name)

make-entity:
	@if [ -z "$(name)" ]; then \
		echo "Usage: make make-entity name=User"; \
		exit 1; \
	fi
	docker compose exec php php bin/console make:entity $(name)

make-command:
	@if [ -z "$(name)" ]; then \
		echo "Usage: make make-command name=ImportProductsCommand"; \
		exit 1; \
	fi
	docker compose exec php php bin/console make:command $(name)

migration:
	docker compose exec php php bin/console make:migration

migration-blank:
	docker compose exec php php bin/console doctrine:migrations:generate

migrate:
	docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

migrate-prev:
	docker compose exec php php bin/console doctrine:migrations:migrate prev


fixtures:
	docker compose exec php php bin/console doctrine:fixtures:load --no-interaction

schema:
	docker compose exec php php bin/console doctrine:schema:validate

db-reset:
	docker compose exec php php bin/console doctrine:database:drop --force --if-exists
	docker compose exec php php bin/console doctrine:database:create
	docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
	docker compose exec php php bin/console doctrine:fixtures:load --no-interaction

hash-password:
	docker compose exec php php bin/console security:hash-password

debug-security:
	docker compose exec php php bin/console debug:config security

debug-container-security:
	docker compose exec php php bin/console debug:container security

