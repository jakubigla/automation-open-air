.DEFAULT_GOAL := build

build:
	@docker-compose down && docker-compose build

run:
	@docker-compose run --rm -e "receipt=$(receipt)" openair
