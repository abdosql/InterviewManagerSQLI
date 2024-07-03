start:
	docker-compose up -d --no-recreate --remove-orphans

create:
	docker-compose up -d --force-recreate

stop:
	docker container stop $$(docker container ps -qa)

# building different projects
build-source:
	docker-compose build backend --force-rm

build-backend:
	docker-compose build backend --force-rm

build-api:
	docker-compose build api --force-rm

build: 
	docker-compose build --force-rm
# -- END building --

install: stop build create install-vendors

enter: 
	docker-compose exec server bash

ps: 
	docker-compose ps
	
remove-containers:
	docker container rm backend_api frontend_client database_server  phpmyadmin  smtp_server  ftp_server  caddy_for_mercure  mongodb_host  mongodb_simple_gui_adminer rabbitmq_server

vendor-api:
	docker-compose exec api composer self-update
	docker-compose exec api composer install	

vendor-backend:
	docker-compose exec backend composer self-update
	docker-compose exec backend composer install

vendor-server: 
	docker-compose exec api composer install
	docker-compose exec server composer install
	
install-vendors: vendor-api vendor-backend vendor-server

# --------------------------------------------------- #
# install different projects
install-api: build-api
	docker-compose exec api composer self-update
	docker-compose exec api symfony new --version="6.3.*" .
	
# ---- 
install-server: up-server
	docker-compose exec server composer self-update
	docker-compose exec server symfony new --webapp --version="6.3.*" .

# ---- 
install-backend: up-backend
	docker-compose exec backend composer self-update
	docker-compose exec backend symfony new --webapp --version="6.3.*" .
# -- END install