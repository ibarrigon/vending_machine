.PHONY: up down build logs shell

up:
	$(COMPOSE) up -d

down:
	$(COMPOSE) down

build:
	$(COMPOSE) build

logs:
	$(COMPOSE) logs -f

shell:
	docker compose exec app bash

