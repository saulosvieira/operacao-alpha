# Configuração do Ambiente (.env)

## Variáveis a serem atualizadas no laravel/.env

```env
APP_NAME="Simulados MVP"
APP_ENV=local
APP_KEY=base64:... # manter existente
APP_DEBUG=true
APP_TIMEZONE=America/Sao_Paulo
APP_URL=http://localhost:8090

# Database
DB_CONNECTION=mysql
DB_HOST=simulados-db
DB_PORT=3306
DB_DATABASE=simulados_db
DB_USERNAME=simulados_user
DB_PASSWORD=simulados_pass_2024

# Redis
REDIS_CLIENT=phpredis
REDIS_HOST=simulados-redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Cache
CACHE_STORE=redis
CACHE_PREFIX=simulados_cache

# Queue
QUEUE_CONNECTION=redis

# Session
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Filesystem
FILESYSTEM_DISK=public

# Mail (configurar depois)
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@simulados.com"
MAIL_FROM_NAME="${APP_NAME}"

# Webhook (adicionar depois)
WEBHOOK_SECRET=
SUBSCRIPTION_PLATFORM=kiwify # ou hotmart
```

## Comandos para executar após atualizar .env

```bash
# Dentro do container simulados-app
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan storage:link
php artisan migrate:fresh --seed
```
