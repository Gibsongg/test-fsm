SHELL=/bin/bash -e

build: up composer migrate

up: ## Запуск проекта
	docker-compose up -d

down: ## Остановка проекта
	docker-compose down

bash: ## Доступ к консоли
	docker-compose exec app bash

restart: down up ## Перезапустить проект

rb:
	docker-compose up -d --build ## Перезапустить проект
composer:
	docker-compose exec app composer install
migrate:
	docker-compose exec app php artisan migrate
seeds:
    docker-compose exec app php artisan db:seed
