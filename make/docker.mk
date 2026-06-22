.PHONY: up down build logs shell

up:
	$(COMPOSE) up -d

down:
	$(COMPOSE) down

build:
	$(COMPOSE) build

logs:
	$(COMPOSE) logs -f

install:
	$(COMPOSE) exec app git config --global --add safe.directory /app
	$(COMPOSE) exec app composer install

shell:
	$(COMPOSE) exec app bash

clear:
	$(COMPOSE) exec app bin/console cache:clear

kill:
	@echo "Stopping vending_machine stack..."
	@$(COMPOSE) down --remove-orphans --volumes --rmi local
