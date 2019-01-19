build:
	docker-compose build --pull
	docker-compose up -d

start:
	docker-compose up -d --remove-orphans

stop:
	docker-compose down

shell:
	docker exec -it servicer sh

lint:
	docker exec -it servicer-linter php-cs-fixer fix
	docker exec -it servicer-linter phpcbf --standard=mvf_ruleset.xml || true
	docker exec -it servicer-linter phpcs --standard=mvf_ruleset.xml
