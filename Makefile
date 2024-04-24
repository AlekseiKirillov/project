SHELL := /bin/bash
EXEC_PHP := docker-compose exec -it php
ifeq (locally,$(firstword $(MAKECMDGOALS)))
	EXEC_PHP :=
endif

locally:;@:
.PHONY: locally
## Проект
## ------

vendor: composer.json composer.lock
	$(EXEC_PHP) composer install
	$(EXEC_PHP) touch vendor

var:
	mkdir -p var

up: var ## Запустить приложение
	docker-compose up --detach --remove-orphans
	$(MAKE) vendor
.PHONY: up

down: ## Остановить приложение
	docker-compose down --remove-orphans
.PHONY: down

restart: down up ## Перезапустить приложение
.PHONY: restart

php: ## Зайти в контейнер PHP
	$(EXEC_PHP) $(if $(cmd),$(cmd),sh)
.PHONY: php

## Помощь
## ------

help: ## Информация по доступным командам
	@gawk -vG=$$(tput setaf 2) -vR=$$(tput sgr0) ' \
              match($$0,"^(([^:]*[^ :]) *:)?([^#]*)##(.*)",a) { \
                if (a[2]!="") {printf "%s%-36s%s %s\n",G,a[2],R,a[4];next}\
                if (a[3]=="") {print a[4];next}\
                printf "\n%-36s %s\n","",a[4]\
              }\
            ' $(MAKEFILE_LIST)
.PHONY: help
.DEFAULT_GOAL := help
