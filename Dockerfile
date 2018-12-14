# Base image:
FROM php:7.2.13-apache

# Install dependancies:
RUN apt-get update && \
    apt-get install -y libicu-dev libpq-dev libmcrypt-dev git zip unzip zlib1g-dev && \
    rm -r /var/lib/apt/lists/* && \
    pecl install mcrypt-1.0.1 && \
    docker-php-ext-enable mcrypt && \
    docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd && \
    docker-php-ext-install intl mbstring pcntl pdo_mysql zip opcache

# Install composer:
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Set our application folder as an environment variable:
ENV APP_HOME /var/www/html

# Change uid and gid of apache to docker user uid/gid:
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# Change the web_root to laravel /var/www/html/public folder:
RUN sed -i -e "s/html/html\/public/g" /etc/apache2/sites-enabled/000-default.conf

# Enable apache module rewrite:
RUN a2enmod rewrite

# Copy source files and run composer:
COPY . $APP_HOME
RUN composer install --no-interaction

# Change ownership of our applications:
RUN chown -R www-data:www-data $APP_HOME
