.PHONY: cs cs-dry phpstan quality

cs:
	$(COMPOSE) exec app vendor/bin/php-cs-fixer fix

cs-dry:
	$(COMPOSE) exec app vendor/bin/php-cs-fixer fix --dry-run --diff
	
phpstan:
	$(COMPOSE) exec app vendor/bin/phpstan analyse

quality:
	@$(MAKE) -s cs
	@$(MAKE) -s phpstan
