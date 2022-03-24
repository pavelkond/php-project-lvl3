setup:
	composer install
	cp -n .env.example .env|| true
	php artisan key:gen --ansi
	touch database/database.sqlite
	php artisan migrate
	npm install

start:
	php artisan serve

test:
	php artisan test

deploy:
	git push heroku

log:
	tail -f storage/logs/laravel.log

migrate:
	php artisan migrate

lint:
	composer exec --verbose phpcs -- --standard=PSR12 app database routes tests

lint-fix:
	composer exec --verbose phpcbf -- --standard=PSR12 app database routes tests
