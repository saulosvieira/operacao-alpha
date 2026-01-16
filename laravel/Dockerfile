# Build stage para frontend
FROM node:20-alpine AS frontend-builder

WORKDIR /app

# Copia arquivos de dependências do Node.js
COPY package.json package-lock.json* ./

# Instala dependências Node.js
RUN npm ci

# Copia arquivos necessários para o build
COPY resources/ resources/
COPY public/ public/
COPY vite.config.ts tailwind.config.ts tsconfig.json tsconfig.app.json tsconfig.node.json ./

# Build do frontend
RUN npm run build

# Stage principal
FROM php:8.2-fpm

# Instala dependências do sistema
RUN apt-get update \
    && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip git unzip curl supervisor libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pcntl zip \
    && pecl install redis \
    && docker-php-ext-enable redis

# Instala Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/laravel

# Copia arquivos de dependências PHP
COPY composer.json composer.lock ./

# Instala dependências PHP
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copia o resto do código
COPY . .

# Copia os arquivos buildados do frontend
COPY --from=frontend-builder /app/public/build/ ./public/build/

# Remove arquivo hot se existir (evita modo desenvolvimento)
RUN rm -f public/hot

# Finaliza instalação do Composer
RUN composer dump-autoload --optimize

# Configura permissões corretas
RUN chown -R www-data:www-data /var/www/laravel \
    && chmod -R 755 /var/www/laravel/storage \
    && chmod -R 755 /var/www/laravel/bootstrap/cache \
    && chmod -R 755 /var/www/laravel/public

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["/usr/bin/supervisord"]
