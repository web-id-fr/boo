.PHONY: help

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

install: ## Setup of project
	composer install

	# Copying .env but asking for overwrite if it exists
	# | true is to ignore errors when file exists
	cp -i .env.example .env | true

stan: ## Static analysis of project
	bin/phpstan analyse src

cs: ## Coding standards check
	bin/phpcs

cs_fix: ## Coding standards fix	
	bin/phpcbf

test: ## All tests at once
	make stan
	make cs