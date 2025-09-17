# Dockerfile (Heroku)
FROM php:8.3-fpm-alpine

# Installer dépendances
RUN apk add --no-cache \
    bash curl unzip git \
    nginx supervisor \
    libzip-dev libpng-dev icu-dev postgresql-dev \
    && docker-php-ext-install intl pdo_pgsql zip bcmath opcache \
    && pecl install apcu mongodb \
    && docker-php-ext-enable apcu mongodb

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copier projet
COPY . .

# Nginx conf
COPY docker/nginx.heroku.conf /etc/nginx/conf.d/default.conf

# Supervisor conf
COPY docker/supervisord.conf /etc/supervisord.conf

# Droits Symfony
RUN mkdir -p var && chown -R www-data:www-data var

# Exposer le port Heroku ($PORT est injecté par la plateforme)
EXPOSE 8080

# Lancer Symfony et Nginx via Supervisor
CMD ["/usr/bin/supervisord","-c","/etc/supervisord.conf"]