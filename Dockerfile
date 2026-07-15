FROM php:8.2-fpm

# Extensions PHP requises par Symfony et MySQL
RUN apt-get update && apt-get install -y \
        git unzip libicu-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql intl zip opcache \
    && rm -rf /var/lib/apt/lists/*

# Composer (copié depuis l'image officielle)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Script de démarrage : installe les dépendances puis lance PHP-FPM
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
