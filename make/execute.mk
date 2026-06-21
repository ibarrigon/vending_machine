.PHONY: execute examples interactive

fill:
	@echo "$(IYellow)Filling vending machine...$(Color_Off)"
	@$(COMPOSE) exec app php bin/console vending:fill

execute:
	@$(COMPOSE) exec app php bin/console vending:sim "$(SCRIPT)"

examples:
	@$(MAKE) execute SCRIPT="1, 0.25, 0.25, GET-SODA"
	@$(MAKE) execute SCRIPT="0.10, 0.10, RETURN-COIN"
	@$(MAKE) execute SCRIPT="1, GET-WATER"

interactive:
	@$(COMPOSE) exec app php bin/console vending:repl
