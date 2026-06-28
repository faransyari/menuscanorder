# syntax=docker/dockerfile:1
#
# Production image for the MenuScanOrder CodeIgniter 4 app.
# Serves the public/ directory through Apache with mod_rewrite enabled.
#
FROM php:8.1-apache

# --------------------------------------------------------------------
# System & PHP extensions
# --------------------------------------------------------------------
RUN apt-get update && apt-get install -y --no-install-recommends \
        libicu-dev \
        libzip-dev \
        unzip \
        git \
    && docker-php-ext-install -j"$(nproc)" \
        intl \
        mysqli \
        pdo \
        pdo_mysql \
        zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Production PHP settings
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# --------------------------------------------------------------------
# Composer
# --------------------------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP dependencies first (better layer caching)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# --------------------------------------------------------------------
# Application source
# --------------------------------------------------------------------
COPY . .
RUN composer dump-autoload --optimize --no-dev

# Apache: serve public/ as the document root and allow .htaccess overrides
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf

# Writable directories must be owned by the web server user
RUN chown -R www-data:www-data writable \
    && chmod -R ug+rw writable

# Entrypoint generates .env from environment variables, runs migrations,
# then starts Apache on the port provided by the platform ($PORT).
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8080
ENTRYPOINT ["entrypoint.sh"]
