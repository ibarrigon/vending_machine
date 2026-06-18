.PHONY: cs phpstan rector qa

phpstan:
	docker compose exec app vendor/bin/phpstan analyse

rector:
	docker compose exec app vendor/bin/rector process

qa: 
	phpstan test