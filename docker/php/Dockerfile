FROM php:8.4-fpm

ARG UNAME=www-data
ARG UGROUP=www-data
ARG USER_ID=1000
ARG GROUP_ID=1000
ARG COMPOSER_VERSION=2.2.12

RUN usermod --uid $USER_ID $UNAME
RUN groupmod --gid $GROUP_ID $UGROUP

RUN apt-get update && chown -R $USER_ID:$GROUP_ID /var/www
RUN apt-get install -y zlib1g-dev g++ libicu-dev zip libzip-dev curl wget mc libsqlite3-dev build-essential  \
    checkinstall libssl-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev libc-client-dev libkrb5-dev git \
    wkhtmltopdf xvfb

RUN docker-php-ext-configure zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-install gd intl opcache pdo pdo_mysql pdo_sqlite zip

RUN pecl install apcu && docker-php-ext-enable apcu
RUN pecl install redis && docker-php-ext-enable redis

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --version=$COMPOSER_VERSION --filename=composer
RUN usermod -u $USER_ID www-data && groupmod -g $GROUP_ID www-data

SHELL ["/bin/bash", "--login", "-c"]
USER www-data

WORKDIR /var/www/html
