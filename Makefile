PORT ?= 8000

.PHONY: setup install build build-css db-setup lint start test

setup: build-css
	$(MAKE) db-setup

build: build-css

install:
	composer install
	npm ci

build-css: install
	npm run build:css

lint:
	./vendor/bin/phpcs

db-setup:
	php src/Database/setup.php

start:
	PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:$(PORT) -t public

test:
	./vendor/bin/phpunit