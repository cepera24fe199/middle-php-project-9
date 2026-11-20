PORT ?= 8000

.PHONY: setup install build build-css lint start

setup: build-css

build: build-css

install:
	composer install
	npm ci

build-css: install
	npm run build:css

lint:
	./vendor/bin/phpcs

start:
	PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:$(PORT) -t public
