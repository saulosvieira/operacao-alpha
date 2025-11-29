FROM php:8.2-fpm

# Instala dependências do sistema
RUN apt-get update \
    && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip git unzip curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis

# Instala Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/laravel

# Instala Supervisor
RUN apt-get update && apt-get install -y supervisor

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["/usr/bin/supervisord"]
