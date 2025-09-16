# Dockerfile
FROM php:8.3.20-fpm-alpine

# Dépendances PHP
RUN apk add --no-cache \
    zlib-dev libxml2-dev libzip-dev unzip \
    php-pear autoconf gcc g++ make musl-dev \
    postgresql-dev nodejs npm nginx supervisor \
    && docker-php-ext-install zip pdo_pgsql pgsql opcache intl bcmath \
    && pecl install apcu mongodb \
    && docker-php-ext-enable apcu mongodb \
    && rm -rf /tmp/pear

# Installe Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Nginx config
COPY docker/nginx.heroku.conf /etc/nginx/conf.d/default.conf

# Supervisord config
COPY docker/supervisord.conf /etc/supervisord.conf

# Variables Heroku
EXPOSE 8080
ENV PORT=8080

# Commande de lancement
CMD ["/usr/bin/supervisord","-c","/etc/supervisord.conf"]