FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
        libpq-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_pgsql

COPY . /var/www/html/
# Esto es un cambio para forzar el redeploy