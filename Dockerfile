# Dockerfile (Heroku)
FROM php:8.3-fpm-alpine

# Installer dépendances
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

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Dossier de travail
WORKDIR /var/www/html

# Copier projet
COPY . .

# Composer en mode production
RUN git config --global --add safe.directory /var/www/html \
    && composer install --no-dev --optimize-autoloader --no-interaction --no-scripts \
    && composer dump-autoload --optimize

# Forcer le cache Symfony en prod
RUN APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear --no-warmup || true

# On supprime carrément les auto-scripts pour le build
RUN composer dump-autoload --optimize

# Générer cache Symfony en prod
RUN APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear --no-warmup || true

# Copy the Nginx template into the **http.d** directory
COPY docker/nginx.conf.template /etc/nginx/http.d/default.conf.template

# Supervisor conf
COPY docker/supervisord.conf /etc/supervisord.conf

# Créer var/cache et var/log avec bons droits
RUN mkdir -p var && chown -R www-data:www-data var

# Exposer le port Heroku ($PORT est injecté par la plateforme)
EXPOSE 8080

COPY docker/php-fpm.env.conf /usr/local/etc/php-fpm.d/zz-env.conf
# Rendre variables_order=EGPCS aussi pour le PHP CLI
RUN printf "variables_order=EGPCS\n" > /usr/local/etc/php/conf.d/zz-variables.ini

# démarrer supervisor
CMD ["/usr/bin/supervisord","-c","/etc/supervisord.conf"]