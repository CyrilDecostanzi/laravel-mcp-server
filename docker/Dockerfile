FROM php:8.4-cli-alpine

LABEL maintainer="Laravel MCP Server"

ARG WWWGROUP=1000
ARG NODE_VERSION=22

WORKDIR /var/www/html

ENV TZ=UTC
ENV SUPERVISOR_PHP_COMMAND="/usr/local/bin/php -d variables_order=EGPCS /var/www/html/artisan serve --host=0.0.0.0 --port=80"
ENV SUPERVISOR_PHP_USER="sail"

# Install system dependencies and build tools
RUN apk add --no-cache \
    bash \
    curl \
    git \
    supervisor \
    libcap \
    mysql-client \
    nodejs \
    npm \
    shadow \
    tzdata \
    # Runtime libraries
    libpng \
    libjpeg-turbo \
    freetype \
    libzip \
    icu-libs \
    oniguruma \
    && cp /usr/share/zoneinfo/$TZ /etc/localtime \
    && echo $TZ > /etc/timezone

# Install build dependencies, compile PHP extensions, then remove build deps
RUN apk add --no-cache --virtual .build-deps \
    autoconf \
    g++ \
    make \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    gd \
    zip \
    bcmath \
    intl \
    mbstring \
    opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Set PHP binary capabilities to bind to privileged ports
RUN setcap "cap_net_bind_service=+ep" /usr/local/bin/php

# Create sail user and group
RUN if getent group $WWWGROUP > /dev/null 2>&1; then \
    delgroup $(getent group $WWWGROUP | cut -d: -f1); \
    fi \
    && addgroup -g $WWWGROUP sail \
    && adduser -D -s /bin/bash -G sail -u 1337 sail \
    && mkdir -p /.composer \
    && chown -R sail:sail /.composer

# Configure Git
RUN git config --global --add safe.directory /var/www/html

# Copy configuration files
COPY start-container /usr/local/bin/start-container
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY php.ini /usr/local/etc/php/conf.d/99-sail.ini
RUN chmod +x /usr/local/bin/start-container

EXPOSE 80/tcp

ENTRYPOINT ["start-container"]
