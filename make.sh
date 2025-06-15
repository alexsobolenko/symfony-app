#!/usr/bin/env bash

function prod_env {
    sed -i 's/APP_ENV=dev/APP_ENV=prod/' .env || true
    sed -i 's/APP_ENV=test/APP_ENV=prod/' .env || true
}

function dev_env {
    sed -i 's/APP_ENV=prod/APP_ENV=dev/' .env || true
    sed -i 's/APP_ENV=test/APP_ENV=dev/' .env || true
}

function test_env {
    sed -i 's/APP_ENV=prod/APP_ENV=test/' .env || true
    sed -i 's/APP_ENV=dev/APP_ENV=test/' .env || true
}

function check {
    set -e
    php bin/console cache:clear --env=dev --no-interaction
    php ./vendor/bin/phpstan analyse
    php ./vendor/bin/phplint
    php ./vendor/bin/phpcs --quiet
}

function unit {
    set -e
    ./make.sh unit_pre
    ./make.sh unit_run
    ./make.sh unit_post
}

function unit_pre {
    set -e
    php bin/console doctrine:database:drop --env=test --force --if-exists --no-interaction
    php bin/console doctrine:database:create --env=test --no-interaction
    php bin/console doctrine:migrations:migrate --env=test --no-interaction
    php bin/console hautelook:fixtures:load --env=test --no-interaction
}

function unit_run {
    set -e
    php bin/console cache:clear --env=test --no-interaction
    command="php bin/phpunit ./tests"
    if [ -n "$1" ]; then
        command="$command --group=$1"
    fi
    eval $command
}

function unit_post {
    set -e
    php bin/console doctrine:database:drop --force --env=test --no-interaction
}

function local_runner {
    set -e
    ./make.sh check
    ./make.sh unit
}

function github_runner {
    set -e
    ./make.sh dev_env
    ./make.sh check
    ./make.sh test_env
    ./make.sh unit
}

function first_run {
    set -e
	composer install
	php bin/console doctrine:database:create
	php bin/console doctrine:migrations:migrate
	php bin/console cache:clear
}

if grep $1 $0 | grep function 2>&1 > /dev/null;
    then
        $1 $2 $3
    else
        echo "command not found: '${1}'"
        echo ""
        echo "list of available:"
        grep function $0 | grep "{" | cut -d" " -f2;
fi
