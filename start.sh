#!/bin/bash

echo "🚀 Iniciando Operação ALFA - Simulados..."

# Para containers existentes
docker compose down

# Reconstrói e inicia os containers
echo "📦 Construindo containers..."
docker compose build --no-cache

echo "🔄 Iniciando serviços..."
docker compose up -d

# Aguarda os containers iniciarem
echo "⏳ Aguardando containers iniciarem..."
sleep 10

# Executa migrações
echo "🗄️ Executando migrações..."
docker compose exec simulados-app php artisan migrate --force

# Executa seeders
echo "🌱 Executando seeders..."
docker compose exec simulados-app php artisan db:seed --class=AdminUserSeeder --force

# Limpa caches
echo "🧹 Limpando caches..."
docker compose exec simulados-app php artisan config:clear
docker compose exec simulados-app php artisan cache:clear

# Remove arquivo hot se existir
docker compose exec simulados-app rm -f public/hot

echo "✅ Aplicação iniciada com sucesso!"
echo "🌐 Acesse: http://localhost:8090"
echo "📊 Banco de dados: localhost:33090"
echo "🔴 Redis: localhost:63790"
echo ""
echo "👤 Usuário admin criado:"
echo "   Email: admin@simulados.com"
echo "   Senha: admin123"