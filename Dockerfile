# Dockerfile (Heroku)
FROM php:8.3.20-cli-alpine

# Dépendances PHP utiles pour Symfony
RUN apk add --no-cache \
    zlib-dev \
    libxml2-dev \
    libzip-dev \
    unzip \
postgresql-dev \
nodejs \
npm \
    autoconf \
    g++ \
    make \
    gcc \
    musl-dev \
    php-pear \
&& docker-php-ext-install \
zip \
pdo_pgsql \
pgsql \
opcache \
intl \
bcmath \
&& pecl install apcu mongodb \
&& docker-php-ext-enable apcu mongodb \
&& rm -rf /tmp/pear

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Répertoire de travail
WORKDIR /var/www/html

# Copier l’application Symfony
COPY . .

# Exposer le port Heroku ($PORT est injecté par la plateforme)
EXPOSE 8080

# Lancer Symfony avec le serveur PHP interne
# - 0.0.0.0:$PORT → pour écouter sur le port Heroku
# -t public → Symfony sert depuis le dossier public/
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]