.PHONY: test test-unit test-functional test-integration
.PHONY: _test _test-unit _test-functional _test-integration

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
	@$(MAKE) -s test-integration

_test-unit:
	@echo "$(IYellow)Running unit tests...$(Color_Off)"
	@$(COMPOSE) run --rm tests vendor/bin/phpunit --testsuite=unit

_test-functional:
	@echo "$(IYellow)Running functional tests...$(Color_Off)"
	@$(COMPOSE) run --rm tests vendor/bin/phpunit --testsuite=functional

_test-integration:
	@echo "$(IYellow)Running integration tests...$(Color_Off)"
	@$(COMPOSE) run --rm tests vendor/bin/phpunit --testsuite=integration

_coverage:
	@echo "$(IYellow)Generate coverage...$(Color_Off)"
	@$(COMPOSE) run --rm tests php \
		-d xdebug.mode=coverage \
		vendor/bin/phpunit \
		--coverage-html coverage
