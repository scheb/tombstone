.DEFAULT_GOAL := up

.PHONY: up
up:
	$(MAKE) down
	docker-compose up -d

.PHONY: down
down:
	docker-compose down --remove-orphans

.PHONY: build
build:
	docker-compose build
	$(MAKE) up

.PHONY: shell
shell:
	docker exec -it tombstone-library sh

.PHONY: test
test:
	docker exec -it tombstone-library vendor/bin/phpunit tests/${filter}

.PHONY: logs
logs:
	@docker-compose logs -f --tail=100

.PHONY: setup
setup:
	$(MAKE) down
	rm -fr ./vendor
	$(MAKE) up
	docker exec -it tombstone-library composer install
