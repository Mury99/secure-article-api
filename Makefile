DOCKER_COMP = docker compose

PHP_CONT = $(DOCKER_COMP) exec php

PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console

build:
	@$(DOCKER_COMP) build --pull --no-cache

up:
	@$(DOCKER_COMP) up --detach

start: build up

down:
	@$(DOCKER_COMP) down --remove-orphans

logs:
	@$(DOCKER_COMP) logs --tail=0 --follow

bash:
	@$(PHP_CONT) bash

test:
	@$(DOCKER_COMP) exec -e APP_ENV=test php vendor/bin/ecs
	@$(DOCKER_COMP) exec -e APP_ENV=test php vendor/bin/phpstan analyse -c phpstan.neon --memory-limit 1G
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/console -e test doctrine:schema:validate --skip-sync

composer:
	@$(eval c ?=)
	@$(COMPOSER) $(c)

sf:
	@$(eval c ?=)
	@$(SYMFONY) $(c)
