.PHONY: help

help:
	@echo "$(IRed)"
	@fgrep -h "###" ./make/head.mk | fgrep -v fgrep | sed -e 's/\\	$$//' | sed -e 's/###//'
	@echo "$(Color_Off)"
	@echo ""
	@echo "Available commands:"
	@echo ""
	@echo "   $(BCyan)help:                 $(Color_Off)This help"
	@echo ""
	@echo "   $(BCyan)init:                 $(Color_Off)Initialize docker"
	@echo "   $(BCyan)up:                   $(Color_Off)"
	@echo "   $(BCyan)down:                 $(Color_Off)"
	@echo "   $(BCyan)restart:              $(Color_Off)"
	@echo "   $(BCyan)install:              $(Color_Off)"
	@echo "   $(BCyan)db-init:              $(Color_Off)"
	@echo "   $(BCyan)test:                 $(Color_Off)"
	@echo ""
