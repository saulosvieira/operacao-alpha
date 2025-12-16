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

# Instala Node.js e npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest

WORKDIR /var/www/laravel

# Copia arquivos de dependências primeiro (para cache do Docker)
COPY composer.json composer.lock ./
COPY package.json ./

# Instala dependências PHP
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Instala dependências Node.js
RUN npm install --production

# Copia o resto do código
COPY . .

# Finaliza instalação do Composer
RUN composer dump-autoload --optimize

# Build do frontend para produção
RUN npm run build

# Configura permissões corretas
RUN chown -R www-data:www-data /var/www/laravel \
    && chmod -R 755 /var/www/laravel/storage \
    && chmod -R 755 /var/www/laravel/bootstrap/cache \
    && chmod -R 755 /var/www/laravel/public/build

# Instala Supervisor
RUN apt-get update && apt-get install -y supervisor

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["/usr/bin/supervisord"]
