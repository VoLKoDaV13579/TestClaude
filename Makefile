# =============================================================================
# Laravel 12 + RoadRunner Development Makefile
# =============================================================================

.DEFAULT_GOAL := help
COMPOSE := docker compose
EXEC := $(COMPOSE) exec app
PHP := $(EXEC) php
COMPOSER := $(EXEC) composer

.PHONY: help up down build test lint stan rector cs-fix cs-check phpmd phpmnd deptrac quality migrate fresh shell logs restart

## ── Docker ──────────────────────────────────────────────────────────────────

help: ## Show this help message
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | \
		awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

up: ## Start all services in detached mode
	$(COMPOSE) up -d

down: ## Stop and remove all services
	$(COMPOSE) down

build: ## Rebuild all Docker images
	$(COMPOSE) build --no-cache

restart: ## Restart all services
	$(COMPOSE) restart

logs: ## Tail logs from all services
	$(COMPOSE) logs -f --tail=100

shell: ## Open a shell in the app container
	$(EXEC) bash

## ── Testing ─────────────────────────────────────────────────────────────────

test: ## Run PHPUnit test suite
	$(PHP) artisan test --parallel

## ── Code Quality ────────────────────────────────────────────────────────────

lint: ## Run PHP syntax linter
	$(EXEC) find app config routes database modules -name "*.php" -print0 | xargs -0 -n1 php -l

stan: ## Run PHPStan static analysis
	$(PHP) vendor/bin/phpstan analyse --memory-limit=512M

rector: ## Run Rector refactoring (dry-run)
	$(PHP) vendor/bin/rector process --dry-run

cs-fix: ## Fix code style with PHP-CS-Fixer
	$(PHP) vendor/bin/php-cs-fixer fix

cs-check: ## Check code style with PHP-CS-Fixer (no changes)
	$(PHP) vendor/bin/php-cs-fixer fix --dry-run --diff

phpmd: ## Run PHP Mess Detector
	$(PHP) vendor/bin/phpmd app,modules text phpmd.xml

phpmnd: ## Run PHP Magic Number Detector
	$(PHP) vendor/bin/phpmnd app modules --exclude=vendor --non-zero-exit-on-violation

deptrac: ## Run Deptrac dependency analysis
	$(PHP) vendor/bin/deptrac analyse --config-file=deptrac.yaml

quality: lint stan cs-check phpmd phpmnd deptrac ## Run all code quality checks

## ── Database ────────────────────────────────────────────────────────────────

migrate: ## Run database migrations
	$(PHP) artisan migrate --force

fresh: ## Drop all tables and re-run migrations with seeders
	$(PHP) artisan migrate:fresh --seed
