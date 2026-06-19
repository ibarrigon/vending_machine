DB_NAME=vending_machine
DB_USER=vending
DB_PASSWORD=vending

.PHONY: db-init db-reset

db-init:
	@echo "$(IYellow)Creating "if not exists" the required database$(Color_Off)"
	$(COMPOSE) exec mysql mysql -uroot -proot -e "CREATE DATABASE IF NOT EXISTS $(DB_NAME);"
	cat initial/db.sql | docker compose exec -T mysql mysql -u$(DB_USER) -p$(DB_PASSWORD) $(DB_NAME)

db-reset:
	@echo "$(IYellow)Reset data base. You lost all previous data.$(Color_Off)"
	$(COMPOSE) exec mysql mysql -uroot -proot -e "DROP DATABASE IF EXISTS $(DB_NAME);"
	$(COMPOSE) exec mysql mysql -uroot -proot -e "CREATE DATABASE $(DB_NAME);"
	cat initial/db.sql | docker compose exec -T mysql mysql -u$(DB_USER) -p$(DB_PASSWORD) $(DB_NAME)
