.PHONY: up
up:
	docker-compose up -d

.PHONY: down
down:
	docker-compose down

.PHONY: rebuild
rebuild:
	docker-compose build --no-cache

.PHONY: install
install:
	docker exec real-estate-insider_php composer install

.PHONY: bash
bash:
	docker exec -it real-estate-insider_php bash
