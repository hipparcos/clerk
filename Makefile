clerk-dev=docker-compose -f config/docker-dev/docker-compose.yml

all: # do nothing by default.

autoload:
	composer dump-autoload

db-migrate:
	$(clerk-dev) exec app php artisan migrate:refresh --seed

db-connect:
	mysql -h localhost -P 33061 --protocol=tcp -u clerk --password=secret --database=clerk

.PHONY: all db-connect
