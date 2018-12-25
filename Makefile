clerk_dev:=docker-compose -f config/docker-dev/docker-compose.yml
base_url:=http://127.0.0.1:8080
client_secret:=DvYKWsQPGXUPrRH41PsHtrMgtMMwfalJ0BjsoVhF
token_file:=token

.PHONY: all
all: exec # do nothing by default.

# deploy-dev configures laravel test environment.
# @require composer
# @require npm
deploy-dev:
	composer install
	npm install
	sed 's/homestead/clerk/' .env.example > .env
	php artisan key:generate
	php artisan passport:keys

# exec executes a command on a container then exit.
# @param container app, web, database; default: app
# @param args the command to run and its arguments
.PHONY: exec
exec:
	$(clerk_dev) exec $(if $(container),$(container),app) $(args)

# run executes a command on a container.
# @param container app, web, database; default: app
# @param args the command to run and its arguments
.PHONY: run
run:
	$(clerk_dev) run $(if $(container),$(container),app) $(args)

# artisan executes a php artisan command.
# @param args the command to run and its arguments
.PHONY: artisan
artisan:
	$(clerk_dev) run app php artisan $(args)

# autoload reload php autoload.
.PHONY: autoload
autoload:
	composer dump-autoload

# req-token write an authentification token to $(token_file).
# @param username
# @param password
.PHONY: req-token
req-token:
	echo "Authorization: Bearer " > $(token_file)
	curl -H "Accept: application/json" -H "Content-Type: application/json" \
	     -v -s -X POST -d '{ "grant_type": "password", "client_id": 1, "client_secret": "DvYKWsQPGXUPrRH41PsHtrMgtMMwfalJ0BjsoVhF", "username": $(if $(username),"$(username)","test@domain.local"), "password":$(if $(password),"$(password)","test"), "scope": "*" }' \
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
# @param nojq bypass jq
# @require jq
.PHONY: req-json
req-json:
	curl -H "Accept: application/json" -H "Content-Type: application/json" -v -s\
	     $(if $(method), -X $(method),)$(if $(token), -H $(token),)$(if $(data), -d $(data),) \
		 $(base_url)$(url) $(if $(nojq),,| jq '.|del(.trace[5:])')
	@echo -e "\n"

# Run all tests.
# @param args
.PHONY: test
test:
	$(clerk_dev) exec app vendor/bin/phpunit $(args)

# Migrate the databse & seed it.
.PHONY: db-migrate
db-migrate:
	$(clerk_dev) exec app php artisan migrate:refresh --seed

# Connect to the database CLI.
.PHONY: db-connect
db-connect:
	mysql -h localhost -P 33061 --protocol=tcp -u clerk --password=secret --database=clerk
