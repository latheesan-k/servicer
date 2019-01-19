build:
	docker-compose build --pull
	docker-compose up -d

start:
	docker-compose up -d --remove-orphans

stop:
	docker-compose down

shell:
	docker exec -it servicer sh

test:
	docker exec -it servicer vendor/bin/codecept run tests/$(suite)/$(file)

test-all:
	docker exec -it servicer vendor/bin/codecept run tests/

coverage:
	docker exec -it servicer-coverage phpdbg -qrr vendor/bin/codecept run --coverage-html

lint:
	docker exec -it servicer-linter php-cs-fixer fix
	docker exec -it servicer-linter phpcbf --standard=mvf_ruleset.xml || true
	docker exec -it servicer-linter phpcs --standard=mvf_ruleset.xml
