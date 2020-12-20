FROM php:7.1-cli-alpine3.10

RUN apk add --no-cache --update composer

RUN curl -L -o /tmp/redis.tar.gz https://github.com/phpredis/phpredis/archive/5.3.2.tar.gz \
    && tar xfz /tmp/redis.tar.gz \
    && rm -r /tmp/redis.tar.gz \
    && mv phpredis-5.3.2 /usr/src/php/ext/redis \
    && docker-php-ext-install redis

WORKDIR /library

COPY ./composer.* ./
RUN composer install -n --no-autoloader --no-scripts --no-progress --no-suggest

COPY . .
RUN composer dump-autoload -o -n

ENTRYPOINT tail -f /dev/null
