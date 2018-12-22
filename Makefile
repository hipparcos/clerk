clerk_dev:=docker-compose -f config/docker-dev/docker-compose.yml
base_url:=http://127.0.0.1:8080
client_secret:=DvYKWsQPGXUPrRH41PsHtrMgtMMwfalJ0BjsoVhF
token_file:=token

all: # do nothing by default.

# autoload reload php autoload.
autoload:
	composer dump-autoload

# req-token write an authentification token to $(token_file).
# @param username
# @param password
req-token:
	echo "Authorization: Bearer " > $(token_file)
	curl -H "Accept: application/json" -H "Content-Type: application/json" \
	     -v -X POST -d '{ "grant_type": "password", "client_id": 1, "client_secret": "DvYKWsQPGXUPrRH41PsHtrMgtMMwfalJ0BjsoVhF", "username": $(if $(username),"$(username)","test@domain.local"), "password":$(if $(password),"$(password)","test"), "scope": "*" }' \
	     $(base_url)/oauth/token \
	| grep -Po '"access_token":.*?[^\\]",' \
	| sed -e 's/"access_token"://' -e 's/"//' -e 's/",$$//' \
	>> $(token_file)
	tr -d '\n' < $(token_file) > temp && mv temp $(token_file)

# req-json make a json request.
# @param method the HTTP verb
# @param token the authentification token file
# @param data the request payload
# @param url the url
req-json:
	curl -H "Accept: application/json" -H "Content-Type: application/json" \
	     -v$(if $(method), -X $(method),)$(if $(token), -H $(token),)$(if $(data), -d $(data),) \
	     $(base_url)$(url)
	@echo -e "\n"

# Run all tests.
test:
	$(clerk_dev) exec app vendor/bin/phpunit

# Migrate the databse & seed it.
db-migrate:
	$(clerk_dev) exec app php artisan migrate:refresh --seed

# Connect to the database CLI.
db-connect:
	mysql -h localhost -P 33061 --protocol=tcp -u clerk --password=secret --database=clerk

.PHONY: all db-connect
