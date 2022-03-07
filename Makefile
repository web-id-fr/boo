.PHONY: help

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

install: ## Installation des dépendances du projet
	composer install
	php artisan key:generate
	make refresh

refresh: ## Reset la base de données

test: ## Lance tout les tests
	bin/phpunit
	bin/phpcs
	bin/phpstan