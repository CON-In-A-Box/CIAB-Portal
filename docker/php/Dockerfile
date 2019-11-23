FROM php:7.2.7-fpm-alpine3.7
RUN apk update \
    && apk upgrade \
    && apk add git bash msmtp;
RUN docker-php-ext-install mysqli pdo_mysql
COPY msmtprc /.msmtprc
COPY php.ini /usr/local/etc/php/php.ini
