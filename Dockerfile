# Dockerfile
# Utilisation de PHP 8.1 avec FPM et Alpine
FROM php:8.1.31-fpm-alpine3.21

# Installation des dépendances système et PHP
RUN apk add --no-cache \
    zlib-dev \
    libxml2-dev \
    libzip-dev \
    unzip \
    php-pear \
    autoconf \
    gcc \
    g++ \
    make \
    musl-dev \
    postgresql-dev \
    nodejs \
    npm \
    && docker-php-ext-install \
    zip \
    pdo_pgsql \
    pgsql \
    opcache \
    intl \
    bcmath \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && rm -rf /tmp/pear

# Définition du répertoire de travail
WORKDIR /var/www/html/

# Copie de Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer
