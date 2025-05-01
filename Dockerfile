# Dockerfile
# Utilisation de PHP avec FPM et Alpine
FROM php:8.3.20-fpm-alpine

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
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && rm -rf /tmp/pear

# Installe Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définis le répertoire de travail
WORKDIR /var/www/html