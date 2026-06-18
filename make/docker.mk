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
	$(COMPOSE) exec app composer install

shell:
	$(COMPOSE) exec app bash

clear:
	$(COMPOSE) exec app bin/console cache:clear
