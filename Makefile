SHELL := /bin/bash
EXEC_PHP := docker-compose exec -it php
ifeq (locally,$(firstword $(MAKECMDGOALS)))
	EXEC_PHP :=
endif

locally:;@:
.PHONY: locally

##
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

##
## Контроль качества кода
## ----------------------

check: lint psalm rector deptrac-directories test check-composer## Запустить все проверки
.PHONY: check

check-composer: composer-validate composer-audit composer-require composer-unused composer-normalize  ## Запустить все проверки для Composer
.PHONE: check-composer

lint: var vendor ## Проверить стиль кода
	$(EXEC_PHP) vendor/bin/phpcs
.PHONY: phpcs

fixer: ## Исправить стиль кода
	$(EXEC_PHP) vendor/bin/phpcbf
.PHONY: phpcbf

psalm: var vendor ## Запустить полный статический анализ PHP кода при помощи Psalm (https://psalm.dev/)
	$(EXEC_PHP) vendor/bin/psalm --no-diff $(file)
.PHONY: psalm

rector: var vendor ## Запустить полный анализ PHP кода при помощи Rector (https://getrector.org)
	$(EXEC_PHP) vendor/bin/rector process --dry-run
.PHONY: rector

rector-fix: var vendor ## Запустить исправление PHP кода при помощи Rector (https://getrector.org)
	$(EXEC_PHP) vendor/bin/rector process
.PHONY: rector-fix

deptrac-directories: var vendor ## Проверить зависимости групп (https://github.com/sensiolabs-de/deptrac)
	$(EXEC_PHP) vendor/bin/deptrac analyze --config-file=deptrac.directories.yaml --cache-file=var/.deptrac.directories.cache
.PHONY: deptrac-directories

composer-unused: vendor ## Обнаружить неиспользуемые зависимости Composer при помощи composer-unused (https://github.com/icanhazstring/composer-unused)
	$(EXEC_PHP) vendor/bin/composer-unused
.PHONY: composer-unused

composer-require: vendor ## Обнаружить неявные зависимости от внешних пакетов при помощи ComposerRequireChecker (https://github.com/maglnet/ComposerRequireChecker)
	$(EXEC_PHP) vendor/bin/composer-require-checker check
.PHONY: composer-require

composer-validate: ## Провалидировать composer.json и composer.lock при помощи composer validate (https://getcomposer.org/doc/03-cli.md#validate)
	$(EXEC_PHP) composer validate --strict --no-check-publish
.PHONY: composer-validate

composer-audit: ## Обнаружить уязвимости в зависимостях Composer при помощи composer audit (https://getcomposer.org/doc/03-cli.md#audit)
	$(EXEC_PHP) composer audit
.PHONY: composer-audit

composer-normalize: var vendor
	$(EXEC_PHP) composer normalize --dry-run --diff
.PHONY: composer-normalize

composer-normalize-fix: var vendor
	$(EXEC_PHP) composer normalize
.PHONY: composer-normalize-fix

test: var vendor ## Запустить тесты PHPUnit (https://phpunit.de)
	$(EXEC_PHP) vendor/bin/phpunit
.PHONY: test

##
## Help
## ----------------------

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
