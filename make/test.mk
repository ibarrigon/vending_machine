.PHONY: test test-unit test-functional test-integration
.PHONY: _test _test-unit _test-functional _test-integration _test-db-reset

test:
	@$(MAKE) -s _test ENV=test

test-unit:
	@$(MAKE) -s _test-unit ENV=test

test-functional:
	@$(MAKE) -s _test-functional ENV=test

test-integration:
	@$(MAKE) -s _test-integration ENV=test

_test:
	@$(MAKE) -s test-unit
	@$(MAKE) -s test-functional

_test-unit:
	@echo "$(IYellow)Running unit tests...$(Color_Off)"
	@$(COMPOSE) run --rm vending_machine_tests vendor/bin/phpunit --testsuite=unit

_test-functional:
	@echo "$(IYellow)Running functional tests...$(Color_Off)"
	@$(MAKE) -s _test-db-reset
	@$(COMPOSE) run --rm vending_machine_tests vendor/bin/phpunit --testsuite=functional

coverage:
	@$(MAKE) -s _coverage ENV=test

_coverage:
	@echo "$(IYellow)Generate coverage...$(Color_Off)"
	@$(COMPOSE) run --rm vending_machine_tests php \
		-d xdebug.mode=coverage \
		vendor/bin/phpunit \
		--coverage-html coverage

_test-db-reset:
	@$(COMPOSE) up -d vending_machine_mysql_test

	@echo "Waiting for MySQL..."
	@until $(COMPOSE) exec vending_machine_mysql_test mysql -hvending_machine_mysql_test -uroot -proot -e "SELECT 1" >/dev/null 2>&1; do \
		sleep 1; \
	done

	@echo "Reset DB..."

	$(COMPOSE) exec vending_machine_mysql_test mysql -hvending_machine_mysql_test -uroot -proot -e "DROP DATABASE IF EXISTS vending_machine_test;"
	$(COMPOSE) exec vending_machine_mysql_test mysql -hvending_machine_mysql_test -uroot -proot -e "CREATE DATABASE vending_machine_test;"

	cat initial/db-test.sql | $(COMPOSE) exec -T vending_machine_mysql_test mysql -hvending_machine_mysql_test -uvending -pvending vending_machine_test
