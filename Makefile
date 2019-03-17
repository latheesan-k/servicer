DEFAULT_GOAL: go

.PHONY: go
go: down up

.PHONY: down
down:
	docker-compose down

.PHONY: up
up:
	docker-compose up -d --remove-orphans

.PHONY: build
build: down rebuild up

.PHONY: rebuild
rebuild:
	docker-compose build --pull

.PHONY: package linter coverage
package linter coverage:
	docker exec -it servicer-$@ sh

.PHONY: test
test:
	docker exec -it servicer-package vendor/bin/codecept run tests/$(suite)/$(file)

.PHONY: cover
cover:
	docker exec -it servicer-coverage phpdbg -qrr vendor/bin/codecept run --coverage-html

.PHONY: lint
lint:
	docker exec -it servicer-linter php-cs-fixer fix
	docker exec -it servicer-linter phpcbf --standard=mvf_ruleset.xml || true
	docker exec -it servicer-linter phpcs --standard=mvf_ruleset.xml
