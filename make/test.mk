.PHONY: test test-unit test-functional test-integration coverage

test:
	@$(MAKE) -s test-unit
	@$(MAKE) -s test-functional
	@$(MAKE) -s test-integration

test-unit:
	@echo "$(IYellow)Running unit tests...$(Color_Off)"
	docker compose run --rm tests vendor/bin/phpunit --testsuite=unit

test-functional:
	@echo "$(IYellow)Running functional tests...$(Color_Off)"
	docker compose run --rm tests vendor/bin/phpunit --testsuite=functional

test-integration:
	@echo "$(IYellow)Running integration tests...$(Color_Off)"
	docker compose run --rm tests vendor/bin/phpunit --testsuite=integration

coverage:
	@echo "$(IYellow)Generate coverage...$(Color_Off)"
	docker compose run --rm tests php \
		-d xdebug.mode=coverage \
		vendor/bin/phpunit \
		--coverage-html coverage
