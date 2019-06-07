FROM php:7.3-apache
COPY . /var/www/html/

RUN apt-get update && \
    apt-get install -y --no-install-recommends mysql-client

RUN docker-php-ext-install mysqli