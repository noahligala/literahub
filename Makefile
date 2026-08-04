.PHONY: setup dev test lint migrate fresh

setup:
	composer setup

dev:
	composer run dev

test:
	php artisan test

lint:
	./vendor/bin/pint --test

migrate:
	php artisan migrate

fresh:
	php artisan migrate:fresh --seed
