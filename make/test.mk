.PHONY: test coverage

test:
	$(COMPOSE) exec app php bin/phpunit

coverage:
	$(COMPOSE) exec app php \
		-d xdebug.mode=coverage \
		bin/phpunit --coverage-html coverage
