# syntax=docker/dockerfile:1
# Laravel 11 + Filament + Vite — tuned for Render (Docker runtime, $PORT).
# https://docs.render.com/deploy-php-laravel-docker

FROM php:8.3-cli-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    git \
    unzip \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    default-libmysqlclient-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        intl \
        pdo_mysql \
        pdo_pgsql \
        zip \
        opcache \
        pcntl \
        gd \
        exif \
        bcmath \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --prefer-dist --no-interaction

COPY package.json package-lock.json ./
RUN npm ci --no-audit --fund=false

COPY . .

RUN set -eux; \
    cp .env.example .env; \
    php artisan key:generate --force --no-interaction; \
    composer dump-autoload --optimize --classmap-authoritative; \
    php artisan package:discover --ansi; \
    (php artisan filament:upgrade --ansi || true)

RUN set -eux; \
    npm run build

RUN set -eux; \
    rm -f .env; \
    chown -R www-data:www-data storage bootstrap/cache; \
    chmod -R ug+rwx storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
