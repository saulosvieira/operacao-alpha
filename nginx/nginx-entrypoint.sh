#!/bin/sh
# Remove o link simbólico existente se ele for um link simbólico
if [ -L /var/www/laravel/public/storage ]; then
    rm -f /var/www/laravel/public/storage
fi

# Cria o diretório de armazenamento se não existir
mkdir -p /var/www/laravel/storage/app/public/aircraft

# Cria o link simbólico para o diretório de armazenamento
ln -sf /var/www/laravel/storage/app/public /var/www/laravel/public/storage

# Garante que o Nginx tem permissão para acessar os arquivos
chown -R nginx:nginx /var/www/laravel/storage
chmod -R 755 /var/www/laravel/storage

# Inicia o Nginx
exec nginx -g 'daemon off;'
