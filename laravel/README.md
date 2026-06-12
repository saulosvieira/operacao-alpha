# Alfa Quest - Sistema de Simulados

Sistema de simulados para concursos públicos com interface React/Vite e backend Laravel.

## Funcionalidades

- ✅ Autenticação de usuários com Laravel Sanctum
- ✅ Gerenciamento de carreiras e editais
- ✅ Sistema de simulados com questões
- ✅ Execução de simulados com cronômetro
- ✅ Sistema de ranking
- ✅ Análise de desempenho
- ✅ Lista de aprovados
- ✅ Sistema de assinaturas
- ✅ Notificações push (PWA)
- ✅ Interface React responsiva
- ✅ Progressive Web App (PWA) instalável

## Requisitos

### Desenvolvimento Local
- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js 20+
- npm

### Desenvolvimento com Docker
- Docker
- Docker Compose

## Instalação

### Opção 1: Desenvolvimento com Docker (Recomendado)

1. Clone o repositório:
```bash
git clone <repository-url>
cd alfa-quest
```

2. Configure o ambiente:
```bash
cd laravel
cp .env.example .env
```

3. Atualize as configurações do banco de dados no `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=simulados-db
DB_PORT=3306
DB_DATABASE=simulados_db
DB_USERNAME=simulados_user
DB_PASSWORD=simulados_pass_2024
```

4. Inicie os containers:
```bash
cd ..
docker-compose up -d
```

5. Instale as dependências e configure o Laravel:
```bash
docker compose exec simulados-app composer install
docker compose exec simulados-app php artisan key:generate
docker compose exec simulados-app php artisan migrate:fresh --seed
```

6. Acesse a aplicação:
- Frontend: http://localhost:8090
- API: http://localhost:8090/api

### Opção 2: Desenvolvimento Local

1. Clone o repositório:
```bash
git clone <repository-url>
cd alfa-quest/laravel
```

2. Instale as dependências:
```bash
composer install
npm install
```

3. Configure o ambiente:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure o banco de dados no arquivo `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simulados_db
DB_USERNAME=root
DB_PASSWORD=
```

5. Execute as migrações e seeders:
```bash
php artisan migrate:fresh --seed
```

6. Compile os assets:
```bash
npm run build
```

7. Inicie o servidor:
```bash
php artisan serve
```

## Desenvolvimento com Docker

### Comandos Básicos

**Iniciar todos os serviços:**
```bash
docker-compose up -d
```

**Parar todos os serviços:**
```bash
docker-compose down
```

**Ver logs:**
```bash
docker-compose logs -f
docker-compose logs -f simulados-app  # Logs específicos do Laravel
```

**Reconstruir containers:**
```bash
docker-compose build --no-cache
docker-compose up -d
```

### Desenvolvimento Frontend com Hot Reload

Para desenvolvimento com Vite e hot module replacement (HMR):

```bash
# Inicia o servidor de desenvolvimento Vite
docker-compose --profile dev up simulados-vite

# Ou em modo detached
docker-compose --profile dev up -d simulados-vite
```

O servidor Vite estará disponível em:
- http://localhost:5173 (dev server direto)
- http://localhost:8090 (através do Laravel)

**Nota:** O perfil `dev` garante que o serviço Vite só seja iniciado quando explicitamente solicitado, não afetando o ambiente de produção.

### Build de Produção

Para compilar os assets para produção dentro do container:

```bash
docker compose exec simulados-app npm run build
```

Os arquivos compilados serão gerados em `public/build/`.

### Comandos Úteis do Laravel

**IMPORTANTE**: Todos os comandos artisan devem ser executados dentro do container usando `docker compose exec simulados-app`.

```bash
# Executar migrations
docker compose exec simulados-app php artisan migrate

# Resetar banco e executar seeders
docker compose exec simulados-app php artisan migrate:fresh --seed

# Limpar cache
docker compose exec simulados-app php artisan cache:clear
docker compose exec simulados-app php artisan config:clear
docker compose exec simulados-app php artisan view:clear

# Gerar chaves VAPID para notificações push
docker compose exec simulados-app php artisan vapid:generate

# Criar migrations
docker compose exec simulados-app php artisan make:migration create_example_table

# Criar models
docker compose exec simulados-app php artisan make:model Example

# Criar controllers
docker compose exec simulados-app php artisan make:controller ExampleController

# Executar testes
docker compose exec simulados-app php artisan test

# Acessar shell do container
docker compose exec simulados-app bash

# Executar Composer
docker compose exec simulados-app composer install
docker compose exec simulados-app composer update

# Executar npm
docker compose exec simulados-app npm install
docker compose exec simulados-app npm run build
```

### Estrutura dos Containers

- **simulados-app**: Container PHP-FPM com Laravel, Supervisor, Node.js e npm
- **simulados-webserver**: Nginx servindo a aplicação na porta 8090
- **simulados-db**: MySQL 8.0 na porta 33090
- **simulados-redis**: Redis na porta 63790
- **simulados-vite**: Servidor de desenvolvimento Vite (apenas com profile `dev`)

### Volumes

- `./laravel:/var/www/laravel` - Código fonte sincronizado
- `/var/www/laravel/node_modules` - Volume anônimo para node_modules (evita sincronização com host)

### Variáveis de Ambiente

- `NODE_ENV=production` - Modo de produção para o container principal
- `NODE_ENV=development` - Modo de desenvolvimento para o container Vite

## Usuários Padrão

Após executar os seeders, você terá acesso aos seguintes usuários:

- **Admin**: admin@example.com / password
- **Usuário Premium**: user@example.com / password
- **Usuário Free**: free@example.com / password

## Estrutura do Projeto

### Arquitetura

O projeto segue uma arquitetura limpa organizada por features (Domain-Driven Design):

```
app/
├── Domain/              # Lógica de negócio por feature
│   ├── Auth/           # Autenticação e usuários
│   ├── Career/         # Carreiras e editais
│   ├── Exam/           # Simulados e questões
│   ├── Ranking/        # Sistema de ranking
│   ├── Performance/    # Análise de desempenho
│   ├── Approved/       # Lista de aprovados
│   ├── Subscription/   # Sistema de assinaturas
│   └── Notification/   # Notificações push
│
├── Http/
│   ├── Controllers/    # Controllers organizados por feature
│   ├── Requests/       # Form Requests para validação
│   └── Resources/      # API Resources para transformação JSON
│
└── Helpers/            # Helpers utilitários

resources/
├── react/              # Frontend React/TypeScript
│   ├── components/     # Componentes React
│   ├── pages/         # Páginas da aplicação
│   ├── services/      # Serviços de API
│   ├── stores/        # Zustand stores
│   └── types/         # TypeScript types
│
└── views/
    └── app.blade.php  # Template principal do SPA
```

### Camadas por Feature

Cada feature no Domain segue a estrutura:

- **Models**: Eloquent models
- **DTOs**: Data Transfer Objects (objetos imutáveis)
- **Enums**: Enumerações tipadas
- **Actions**: Casos de uso single-purpose
- **Repositories**: Acesso a dados (única camada que conhece Models)

### Frontend (React)

- **Components**: Componentes reutilizáveis (shadcn/ui)
- **Pages**: Páginas da aplicação
- **Services**: Camada de comunicação com API
- **Stores**: Gerenciamento de estado (Zustand)
- **Types**: Definições TypeScript

## Componentes Blade

### Componentes Reutilizáveis
- `action-buttons` - Botões de ação (visualizar, editar, excluir)
- `data-table` - Tabela de dados com ordenação
- `delete-confirmation` - Modal de confirmação de exclusão
- `modal-simple` - Modal simples
- `pagination` - Paginação customizada

### Layout
- `layouts.voemtx` - Layout base com AdminLTE

## API Endpoints

### Autenticação
- `POST /api/login` - Login
- `POST /api/register` - Registro
- `POST /api/logout` - Logout (autenticado)
- `GET /api/me` - Dados do usuário atual (autenticado)

### Carreiras
- `GET /api/careers` - Listar carreiras
- `GET /api/careers/{id}` - Detalhes da carreira
- `GET /api/careers/{id}/exams` - Simulados da carreira

### Simulados
- `GET /api/exams` - Listar simulados
- `GET /api/exams/{id}` - Detalhes do simulado
- `POST /api/exams/{id}/start` - Iniciar tentativa
- `GET /api/attempts/{id}` - Detalhes da tentativa
- `POST /api/attempts/{id}/answer` - Submeter resposta
- `POST /api/attempts/{id}/finish` - Finalizar tentativa

### Ranking
- `GET /api/ranking` - Listar ranking
- `GET /api/ranking/my-position` - Posição do usuário

### Desempenho
- `GET /api/performance/statistics` - Estatísticas
- `GET /api/performance/history` - Histórico

### Aprovados
- `GET /api/approved` - Listar aprovados

### Assinaturas
- `GET /api/plans` - Listar planos
- `POST /api/subscribe` - Criar assinatura
- `GET /api/subscription/status` - Status da assinatura
- `POST /api/subscription/cancel` - Cancelar assinatura

### Notificações
- `POST /api/notifications/subscribe` - Inscrever para notificações
- `POST /api/notifications/unsubscribe` - Desinscrever

### Perfil
- `GET /api/user/profile` - Dados do perfil
- `PUT /api/user/profile` - Atualizar perfil
- `DELETE /api/user/account` - Excluir conta

## Versionamento do App Mobile (Flutter)

O backend envia o header `X-API-Min-Version` em todas as respostas autenticadas da API. O app Flutter compara esse valor com sua versão instalada e, caso esteja abaixo, exibe uma tela de atualização obrigatória (Force Update).

### Como funciona

- A versão mínima é lida de `config('mobile.min_version')`, que por sua vez vem da env var `MOBILE_MIN_VERSION`.
- O valor segue semver: `MAJOR.MINOR.PATCH` (ex: `1.0.0`, `1.2.0`, `2.0.0`).
- Na primeira publicação do app Flutter, o valor padrão é `1.0.0` — ou seja, nenhum usuário é bloqueado.

### Quando fazer bump

Atualize `MOBILE_MIN_VERSION` quando uma nova versão do app Flutter for **obrigatória** — por exemplo:
- Mudança de contrato na API que versões antigas não suportam.
- Correção crítica de segurança no app.
- Remoção de endpoint que versões antigas ainda usam.

### Processo de bump

1. Publique a nova versão do app Flutter nas lojas (Google Play / App Store).
2. Aguarde que a maioria dos usuários atualize (opcionalmente, monitore métricas de adoção).
3. Atualize a env var no servidor:
   ```env
   MOBILE_MIN_VERSION=1.2.0
   ```
4. Limpe o cache de configuração (se estiver em uso):
   ```bash
   php artisan config:clear
   ```
5. A partir deste ponto, usuários com versão inferior a `1.2.0` verão a tela de Force Update.

### Onde configurar

| Ambiente | Localização |
|----------|-------------|
| Local | `laravel/.env` |
| Docker | `laravel/.env` ou variável no `docker-compose.yml` |
| Produção | Variável de ambiente do servidor / painel de deploy |

## Desenvolvimento

### Adicionando Nova Feature

Para adicionar uma nova feature seguindo a arquitetura do projeto:

1. **Crie a estrutura de diretórios:**
```bash
mkdir -p app/Domain/NovaFeature/{Models,DTOs,Enums,Actions,Repositories}
```

2. **Crie o Model:**
```php
// app/Domain/NovaFeature/Models/NovaFeature.php
namespace App\Domain\NovaFeature\Models;

use Illuminate\Database\Eloquent\Model;

class NovaFeature extends Model
{
    protected $fillable = [...];
}
```

3. **Crie DTOs:**
```php
// app/Domain/NovaFeature/DTOs/NovaFeatureData.php
namespace App\Domain\NovaFeature\DTOs;

readonly class NovaFeatureData
{
    public function __construct(
        public string $id,
        public string $name,
    ) {}
}
```

4. **Crie Repository:**
```php
// app/Domain/NovaFeature/Repositories/NovaFeatureRepository.php
namespace App\Domain\NovaFeature\Repositories;

class NovaFeatureRepository
{
    public function findAll(): Collection { ... }
    private function toDTO(Model $model): DTO { ... }
}
```

5. **Crie Actions:**
```php
// app/Domain/NovaFeature/Actions/ListNovaFeatureAction.php
namespace App\Domain\NovaFeature\Actions;

class ListNovaFeatureAction
{
    public function __construct(
        private NovaFeatureRepository $repository
    ) {}
    
    public function execute(): Collection { ... }
}
```

6. **Crie Controller e Resource:**
```php
// app/Http/Controllers/Api/NovaFeature/NovaFeatureController.php
// app/Http/Resources/NovaFeature/NovaFeatureResource.php
```

7. **Adicione rotas em `routes/api.php`**

### Compilando Assets

**Desenvolvimento:**
```bash
npm run dev
```

**Produção:**
```bash
npm run build
```

**Com Docker:**
```bash
# Desenvolvimento com HMR
docker-compose --profile dev up simulados-vite

# Build de produção
docker exec -it simulados-app npm run build
```

### Notificações Push

Para configurar notificações push:

1. **Gere chaves VAPID:**
```bash
docker compose exec simulados-app php artisan vapid:generate
```

2. **Configure no `.env`:**
```env
VAPID_PUBLIC_KEY=...
VAPID_PRIVATE_KEY=...
VAPID_SUBJECT=mailto:seu-email@example.com
```

3. **As chaves serão usadas automaticamente pelo frontend**

### Testes

```bash
# Executar todos os testes
docker compose exec simulados-app php artisan test

# Executar testes específicos
docker compose exec simulados-app php artisan test --filter=ExamTest
```

## Troubleshooting

### Problemas Comuns

**Erro de permissão no Docker:**
```bash
docker compose exec simulados-app chown -R www-data:www-data /var/www/laravel
docker compose exec simulados-app chmod -R 755 /var/www/laravel/storage
docker compose exec simulados-app chmod -R 755 /var/www/laravel/bootstrap/cache
```

**Assets não carregam:**
```bash
# Limpe o cache e recompile
docker compose exec simulados-app php artisan cache:clear
docker compose exec simulados-app npm run build
```

**Vite não conecta (HMR):**
- Verifique se a porta 5173 está disponível
- Certifique-se de que o serviço foi iniciado com `--profile dev`
- Verifique os logs: `docker-compose logs -f simulados-vite`

**Erro de conexão com banco de dados:**
- Verifique se o container do MySQL está rodando: `docker ps`
- Verifique as credenciais no `.env`
- Aguarde alguns segundos após iniciar os containers (MySQL precisa inicializar)

**node_modules muito grande:**
- O volume anônimo `/var/www/laravel/node_modules` evita sincronização com o host
- Se precisar limpar: `docker-compose down -v` (remove todos os volumes)

**Build de produção falha:**
```bash
# Limpe node_modules e reinstale
docker compose exec simulados-app rm -rf node_modules
docker compose exec simulados-app npm install
docker compose exec simulados-app npm run build
```

### Logs e Debug

**Ver logs em tempo real:**
```bash
# Todos os serviços
docker-compose logs -f

# Serviço específico
docker-compose logs -f simulados-app
docker-compose logs -f simulados-webserver
docker-compose logs -f simulados-vite
```

**Logs do Laravel:**
```bash
docker compose exec simulados-app tail -f storage/logs/laravel.log
```

**Verificar status dos containers:**
```bash
docker-compose ps
```

## Deploy em Produção

### Preparação

1. **Configure variáveis de ambiente:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com
```

2. **Build dos assets:**
```bash
npm run build
```

3. **Otimize o Laravel:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

4. **Configure HTTPS no Nginx**

5. **Configure backup do banco de dados**

### Checklist de Deploy

- [ ] Variáveis de ambiente configuradas
- [ ] Assets compilados (`npm run build`)
- [ ] Migrations executadas
- [ ] Cache otimizado
- [ ] HTTPS configurado
- [ ] Backup configurado
- [ ] Monitoramento configurado
- [ ] Logs configurados

## Licença

Este projeto está licenciado sob a [MIT License](LICENSE).