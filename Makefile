#!/usr/bin/make
.PHONY: help

DOCKER_COMPOSE ?= docker compose
DOCKER_EXEC_PHP=${DOCKER_COMPOSE} exec php-cli

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

install: build start composer_install env_files ## Setup dev docker compose stack
	@echo "🔨 Project setup done!"

build: ## Build dev docker compose stack
	@echo "🐳 Build or rebuild stack images..."
	@WWWUSER=$(shell id -u) WWWGROUP=$(shell id -g) docker compose --profile "*" build

build_php82: ## Build dev docker compose stack with PHP 8.2
	@echo "🐳 Build or rebuild stack images..."
	@WWWUSER=$(shell id -u) WWWGROUP=$(shell id -g) PHP_VERSION="8.2" docker compose --profile "*" build

build_php83: ## Build dev docker compose stack with PHP 8.3
	@echo "🐳 Build or rebuild stack images..."
	@WWWUSER=$(shell id -u) WWWGROUP=$(shell id -g) PHP_VERSION="8.3" docker compose --profile "*" build

start: ## Start stack
	@echo "🐳 Make sure docker stack is up..."
	$(DOCKER_COMPOSE) up -d --wait

stop: ## Stop stack
	$(DOCKER_COMPOSE) --profile "*" stop

down: ## Down stack
	$(DOCKER_COMPOSE) --profile "*" down

restart: stop start

destroy: ## Destroy docker compose stack with volumes
	$(DOCKER_COMPOSE) --profile "*" down -v

composer_install: # Install composer dependencies
	@echo "🔨 Setup Composer dependencies..."
	@$(DOCKER_EXEC_PHP) composer install

env_files:
	@echo "👻 Setup .env files and generate app keys"
	@[ -f ./.env ] && echo '.env file already created' || cp .env.example .env && ${DOCKER_EXEC_PHP} php artisan key:generate
	@[ -f ./.env.testing ] && echo '.env.testing file already created' || cp .env.testing.example .env.testing && ${DOCKER_EXEC_PHP} php artisan key:generate --env=testing

ci: cs stan test ## Run CI suite

cs: ## Run PHPCS
	$(DOCKER_EXEC_PHP) ./bin/phpcs

stan: ## Run PHPStan
	$(DOCKER_EXEC_PHP) ./bin/phpstan

test: ## Run PHPUnit tests
	$(DOCKER_EXEC_PHP) ./bin/phpunit

composer_audit: ## Run composer security audit
	${DOCKER_EXEC_PHP} composer audit --locked

composer_validate:
	@$(DOCKER_EXEC_PHP) composer validate
