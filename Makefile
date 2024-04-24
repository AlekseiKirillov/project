SHELL := /bin/bash
EXEC_PHP :=

## Проект
## ------

vendor: composer.json composer.lock
	$(EXEC_PHP) composer install
	$(EXEC_PHP) touch vendor

var:
	mkdir -p var

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
