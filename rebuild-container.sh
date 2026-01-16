#!/bin/bash

echo "🔧 Reconstruindo container com extensão ZIP do PHP..."

# Para o container atual
echo "📦 Parando containers..."
docker compose down

# Remove a imagem atual para forçar rebuild
echo "🗑️  Removendo imagem antiga..."
docker rmi simulados-laravel-app 2>/dev/null || true

# Reconstrói o container
echo "🏗️  Reconstruindo container..."
docker compose build --no-cache simulados-app

# Inicia os containers
echo "🚀 Iniciando containers..."
docker compose up -d

echo "✅ Container reconstruído com sucesso!"
echo "🔍 Verificando se a extensão ZIP foi instalada..."

# Verifica se a extensão foi instalada
docker compose exec simulados-app php -m | grep -i zip && echo "✅ Extensão ZIP instalada com sucesso!" || echo "❌ Extensão ZIP não encontrada"

echo "📋 Para verificar os logs: docker compose logs -f simulados-app"