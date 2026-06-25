FROM php:8.4-fpm-bookworm

ARG UID=1000
ARG GID=1000

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    curl \
    ca-certificates \
    libcurl4-openssl-dev \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    librabbitmq-dev \
    && docker-php-ext-install \
        curl \
        intl \
        pdo \
        pdo_pgsql \
        zip \
        opcache \
    && pecl install redis amqp \
    && docker-php-ext-enable redis amqp \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN groupadd -g ${GID} app \
    && useradd -u ${UID} -g app -m app

WORKDIR /app

USER app