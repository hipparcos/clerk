clerk-dev=docker-compose -f config/docker-dev/docker-compose.yml

all: # do nothing by default.

# Reload php autoload.
autoload:
	composer dump-autoload

# Make a json request.
# @param method the HTTP verb
# @param url the url
curl-json:
	curl -H "Accept: application/json" -H "Content-Type: application/json" \
	     -X $(method) $(url)
	@echo -e "\n"

# Run all tests.
test:
	$(clerk-dev) exec app vendor/bin/phpunit

# Migrate the databse & seed it.
db-migrate:
	$(clerk-dev) exec app php artisan migrate:refresh --seed

# Connect to the database CLI.
db-connect:
	mysql -h localhost -P 33061 --protocol=tcp -u clerk --password=secret --database=clerk

.PHONY: all db-connect
