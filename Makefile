.PHONY: up down stop build restart logs shell bash composer test

up:
	docker compose up -d

down:
	docker compose down

stop:
	docker compose stop

build:
	docker compose build

restart:
	docker compose restart

logs:
	docker compose logs -f

shell:
	docker compose exec bingely-app sh

bash:
	docker compose exec bingely-app bash

composer:
	docker compose exec bingely-app composer $(filter-out $@,$(MAKECMDGOALS))

test:
	docker compose exec bingely-app vendor/bin/phpunit

format:
	docker compose exec bingely-app ./vendor/bin/php-cs-fixer fix --allow-risky=yes

analyse:
	docker compose exec bingely-app vendor/bin/phpstan analyse -l 6 src tests

%:
	@:
