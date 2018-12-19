FROM php:7.1-fpm

RUN apt-get update && \
    apt-get install -y libmcrypt-dev mysql-client libmagickwand-dev --no-install-recommends && \
    pecl install imagick && \
    docker-php-ext-enable imagick && \
    docker-php-ext-install mcrypt pdo_mysql

# Fix permissions.
RUN usermod -u 1000 www-data
RUN groupmod -g 985 www-data
