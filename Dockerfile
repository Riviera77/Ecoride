# Dockerfile (Heroku)
FROM php:8.3-fpm-alpine

# 1) Paquets nécessaires (nginx, supervisord, envsubst) + extensions PHP
RUN apk add --no-cache \
    bash curl unzip git \
    nginx supervisor gettext \
    libzip-dev libpng-dev icu-dev postgresql-dev \
    autoconf make g++ gcc musl-dev php-pear \
    && docker-php-ext-install intl pdo_pgsql zip bcmath opcache \
    && pecl install apcu mongodb \
    && docker-php-ext-enable apcu mongodb \
    && apk del autoconf make g++ gcc musl-dev php-pear \
    && rm -rf /tmp/pear

# 2) Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3) Dossier de travail
WORKDIR /var/www/html
# Copier projet
COPY . .

# 4) Dépendances PHP prod (sans scripts) + autoload optimisé
RUN git config --global --add safe.directory /var/www/html \
    && composer install --no-dev --optimize-autoloader --no-interaction --no-scripts \
    && composer dump-autoload --optimize
# 5) Assets ImportMap/AssetMapper
RUN APP_ENV=prod DATABASE_URL='postgres://u:p@localhost:5432/db' \
    php bin/console importmap:install --no-interaction --ansi \
 && APP_ENV=prod DATABASE_URL='postgres://u:p@localhost:5432/db' \
    php bin/console asset-map:compile --no-interaction --ansi

# 6) Copy the Nginx template into the **http.d** directory
COPY docker/nginx.conf.template /etc/nginx/http.d/default.conf.template
# Supervisor conf
COPY docker/supervisord.conf /etc/supervisord.conf

# 7) Créer var/cache et var/log avec bons droits
RUN mkdir -p var && chown -R www-data:www-data var

# 8) Exposer le port Heroku ($PORT est injecté par la plateforme)
EXPOSE 8080

# 9) PHP-FPM : expose les variables d'env au runtime
COPY docker/php-fpm.env.conf /usr/local/etc/php-fpm.d/zz-env.conf
# Rendre variables_order=EGPCS aussi pour le PHP CLI
RUN printf "variables_order=EGPCS\n" > /usr/local/etc/php/conf.d/zz-variables.ini

# 10) démarrer supervisor
CMD ["/usr/bin/supervisord","-c","/etc/supervisord.conf"]