FROM php:7.2.7-fpm-alpine3.7
RUN apk update \
    && apk upgrade \
    && apk add git bash;
RUN docker-php-ext-install mysqli pdo_mysql