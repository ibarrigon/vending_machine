DB_NAME=vending_machine
DB_USER=vending
DB_PASSWORD=vending

.PHONY: db-init db-reset

db-init:
	$(COMPOSE) exec mysql mysql -uroot -proot -e "CREATE DATABASE IF NOT EXISTS $(DB_NAME);"
	cat initial/db.sql | docker compose exec -T mysql mysql -u$(DB_USER) -p$(DB_PASSWORD) $(DB_NAME)

db-reset:
	$(COMPOSE) exec mysql mysql -uroot -proot -e "DROP DATABASE IF EXISTS $(DB_NAME);"
	$(COMPOSE) exec mysql mysql -uroot -proot -e "CREATE DATABASE $(DB_NAME);"
	cat initial/db.sql | docker compose exec -T mysql mysql -u$(DB_USER) -p$(DB_PASSWORD) $(DB_NAME)
