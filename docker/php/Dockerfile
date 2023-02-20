FROM php:7.2.7-fpm-alpine3.7
RUN apk update \
    && apk upgrade \
    && apk add git bash msmtp freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev;
RUN docker-php-ext-install mysqli pdo_mysql
RUN docker-php-ext-install gd
RUN apk add $PHPIZE_DEPS \
    && pecl channel-update pecl.php.net \
    && pecl install xdebug-2.9.0 \
    && docker-php-ext-enable xdebug
COPY msmtprc /.msmtprc
COPY php.ini /usr/local/etc/php/php.ini
