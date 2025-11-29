# 🚀 Guia de Início Rápido

## Passo 1: Subir o Ambiente Docker

```bash
# Na raiz do projeto
docker-compose up -d

# Verificar se os containers subiram
docker ps

# Você deve ver:
# - simulados-app
# - simulados-webserver
# - simulados-db
# - simulados-redis
```

## Passo 2: Configurar o Laravel

```bash
# Copiar .env.example para .env (se necessário)
cp laravel/.env.example laravel/.env

# Editar laravel/.env com as configurações:
# DB_HOST=simulados-db
# DB_DATABASE=simulados_db
# DB_USERNAME=simulados_user
# DB_PASSWORD=simulados_pass_2024
# REDIS_HOST=simulados-redis

# Entrar no container
docker exec -it simulados-app bash

# Dentro do container:
composer install
php artisan key:generate
php artisan storage:link
php artisan config:clear
php artisan cache:clear

# Sair do container
exit
```

## Passo 3: Testar o Ambiente

```bash
# Acessar no navegador:
http://localhost:8090

# Você deve ver a página inicial do Laravel/AdminLTE
```

## Passo 4: Limpar Recursos Não Utilizados

```bash
# Remover módulo Quotes
rm -rf laravel/modules/Quotes

# Remover PDFs de exemplo
rm laravel/*.pdf
rm laravel/test-pdf-template.php

# Remover arquivos temporários
rm laravel/Archive.zip
rm laravel/todo-list
rm -rf laravel/temp_laravel

# Limpar cache novamente
docker exec simulados-app php artisan cache:clear
docker exec simulados-app php artisan route:clear
```

## Passo 5: Verificar Estrutura

```bash
# Listar rotas existentes
docker exec simulados-app php artisan route:list

# Verificar conexão com banco
docker exec simulados-app php artisan migrate:status

# Verificar conexão com Redis
docker exec simulados-redis redis-cli ping
# Deve retornar: PONG
```

## Passo 6: Criar Primeira Migration

```bash
# Entrar no container
docker exec -it simulados-app bash

# Criar migration de carreiras
php artisan make:migration create_carreiras_table

# Editar o arquivo criado em:
# database/migrations/YYYY_MM_DD_HHMMSS_create_carreiras_table.php
```

## Comandos Úteis

### Docker

```bash
# Parar containers
docker-compose down

# Reiniciar containers
docker-compose restart

# Ver logs
docker-compose logs -f

# Ver logs de um serviço específico
docker-compose logs -f simulados-app

# Entrar no container
docker exec -it simulados-app bash
docker exec -it simulados-db bash
docker exec -it simulados-redis bash
```

### Laravel (dentro do container)

```bash
# Limpar caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Migrations
php artisan migrate
php artisan migrate:fresh
php artisan migrate:fresh --seed
php artisan migrate:rollback

# Criar recursos
php artisan make:model NomeModel -m
php artisan make:controller NomeController
php artisan make:migration create_nome_table
php artisan make:seeder NomeSeeder
php artisan make:request NomeRequest

# Rotas
php artisan route:list
php artisan route:cache

# Testes
php artisan test
php artisan test --filter NomeDoTeste
```

### MySQL (dentro do container do banco)

```bash
# Entrar no MySQL
docker exec -it simulados-db mysql -u simulados_user -p
# Senha: simulados_pass_2024

# Comandos MySQL:
SHOW DATABASES;
USE simulados_db;
SHOW TABLES;
DESCRIBE nome_tabela;
SELECT * FROM nome_tabela;
```

### Redis (dentro do container)

```bash
# Entrar no Redis CLI
docker exec -it simulados-redis redis-cli

# Comandos Redis:
PING
KEYS *
GET chave
DEL chave
FLUSHALL
```

## Estrutura de Diretórios

```
projeto/
├── .kiro/
│   └── specs/
│       └── simulados-mvp/
│           ├── RESUMO-EXECUTIVO.md
│           ├── INICIO-RAPIDO.md (este arquivo)
│           ├── plano-desenvolvimento.md
│           ├── checklist-limpeza.md
│           ├── env-config.md
│           ├── requirements.md
│           ├── design.md
│           └── tasks.md
├── laravel/
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── public/
│   ├── resources/
│   ├── routes/
│   └── .env
├── nginx/
│   ├── nginx.conf
│   └── nginx-entrypoint.sh
├── docker-compose.yml
└── README.md
```

## Troubleshooting

### Containers não sobem

```bash
# Verificar se há conflito de portas
lsof -i :8090
lsof -i :33090
lsof -i :63790

# Parar containers conflitantes
docker ps
docker stop nome_container

# Remover containers antigos
docker-compose down -v
docker-compose up -d
```

### Erro de permissão no Laravel

```bash
# Ajustar permissões
docker exec simulados-app chmod -R 775 storage bootstrap/cache
docker exec simulados-app chown -R www-data:www-data storage bootstrap/cache
```

### Erro de conexão com banco

```bash
# Verificar se o banco está rodando
docker exec simulados-db mysql -u root -p
# Senha: simulados_root_2024

# Verificar se o banco existe
SHOW DATABASES;

# Criar banco se não existir
CREATE DATABASE simulados_db;
GRANT ALL PRIVILEGES ON simulados_db.* TO 'simulados_user'@'%';
FLUSH PRIVILEGES;
```

### Erro de conexão com Redis

```bash
# Verificar se Redis está rodando
docker exec simulados-redis redis-cli ping

# Se não responder, reiniciar
docker-compose restart simulados-redis
```

## Próximos Passos

Após configurar o ambiente:

1. ✅ Ambiente Docker funcionando
2. ✅ Laravel configurado e acessível
3. ✅ Recursos não utilizados removidos
4. ⏳ Criar migrations do sistema
5. ⏳ Desenvolver CRUD de carreiras
6. ⏳ Seguir plano de desenvolvimento

## Recursos Adicionais

- [Documentação Laravel 12](https://laravel.com/docs/12.x)
- [AdminLTE](https://adminlte.io/)
- [Docker Compose](https://docs.docker.com/compose/)
- [MySQL 8.0](https://dev.mysql.com/doc/refman/8.0/en/)
- [Redis](https://redis.io/documentation)

---

**Dúvidas?** Consulte o `plano-desenvolvimento.md` para detalhes completos de cada fase.
