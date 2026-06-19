FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libzip-dev libicu-dev libpq-dev \
    default-mysql-client locales \
 && docker-php-ext-configure intl \
 && docker-php-ext-install pdo pdo_mysql intl zip opcache \
 && docker-php-source delete

RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && locale-gen
ENV LANG=en_US.UTF-8
ENV LC_ALL=en_US.UTF-8

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY docker/php/php.ini /usr/local/etc/php/conf.d/php.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /app

CMD ["php-fpm"]
