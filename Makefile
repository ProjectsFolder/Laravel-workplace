.PHONY: build up down restart shell logs migrate seed test install

build:
	docker-compose build

up:
	docker-compose up -d

down:
	docker-compose down

restart:
	docker-compose restart

shell:
	docker-compose exec app bash

logs:
	docker-compose logs -f

migrate:
	docker-compose exec app php artisan migrate

migrate-fresh:
	docker-compose exec app php artisan migrate:fresh --seed

seed:
	docker-compose exec app php artisan db:seed

install:
	cp .env.example .env
	docker-compose build
	docker-compose up -d
	sleep 10
	docker-compose exec app php artisan key:generate
	docker-compose exec app php artisan migrate
	@echo "Installation complete! App available at http://localhost:8080"

test:
	docker-compose exec app php artisan test

clean:
	docker-compose down -v
	docker system prune -f

phpmyadmin:
	docker-compose exec phpmyadmin bash || docker-compose exec -T phpmyadmin sh

bash:
	docker-compose exec app bash

mysql:
	docker-compose exec mysql mysql -u laravel -psecret laravel

redis:
	docker-compose exec redis redis-cli

rabbitmq:
	docker-compose exec rabbitmq rabbitmqctl list_queues
