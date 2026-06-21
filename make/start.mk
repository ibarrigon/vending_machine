.PHONY: init

init: 
	@$(MAKE) -s up 
	@$(MAKE) -s install 
	@$(MAKE) -s db-init
	