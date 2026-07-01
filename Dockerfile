# =============================================================================
# Stage 1: Composer dependencies
# =============================================================================
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-autoloader \
    --no-scripts \
    --prefer-dist

COPY . .

RUN composer dump-autoload \
    --optimize \
    --classmap-authoritative \
    --no-dev

# =============================================================================
# Stage 2: RoadRunner binary
# =============================================================================
FROM ghcr.io/roadrunner-server/roadrunner:2024 AS roadrunner

# =============================================================================
# Stage 3: Production image
# NOTE: Target runtime is PHP 8.5. Using php:8.4-cli-bookworm as the base
#       image until official PHP 8.5 images are available.
# =============================================================================
FROM php:8.4-cli-bookworm AS production

LABEL maintainer="devops@example.com"
LABEL description="Laravel 12 production image with RoadRunner"

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
        libpq-dev \
        libicu-dev \
        libzip-dev \
        libonig-dev \
        zip \
        unzip \
        curl \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pgsql \
        pcntl \
        sockets \
        intl \
        opcache \
        bcmath \
        zip

# Install Redis extension via PECL
RUN pecl install redis \
    && docker-php-ext-enable redis

# Configure OPcache for production
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.max_accelerated_files=20000'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.jit=1255'; \
    echo 'opcache.jit_buffer_size=128M'; \
    echo 'opcache.preload_user=app'; \
    } > /usr/local/etc/php/conf.d/opcache-production.ini

# Configure PHP for production
RUN { \
    echo 'memory_limit=256M'; \
    echo 'post_max_size=64M'; \
    echo 'upload_max_filesize=64M'; \
    echo 'expose_php=Off'; \
    echo 'display_errors=Off'; \
    echo 'log_errors=On'; \
    echo 'error_log=/dev/stderr'; \
    } > /usr/local/etc/php/conf.d/php-production.ini

# Create non-root user
RUN groupadd -r app && useradd -r -g app -d /app -s /sbin/nologin app

WORKDIR /app

# Copy RoadRunner binary
COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr

# Copy application code
COPY --chown=app:app . .

# Copy vendor dependencies from composer stage
COPY --from=vendor --chown=app:app /app/vendor ./vendor

# Ensure storage and cache directories exist and are writable
RUN mkdir -p \
        storage/logs \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        bootstrap/cache \
    && chown -R app:app storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Switch to non-root user
USER app

EXPOSE 8080 2112 2114

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD curl -f http://localhost:2114/health?plugin=http || exit 1

ENTRYPOINT ["rr", "serve", "-c", ".rr.yaml"]
