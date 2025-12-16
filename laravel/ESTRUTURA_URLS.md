# Estrutura de URLs - Operação ALFA

## Visão Geral

A aplicação possui duas interfaces distintas que coexistem no mesmo domínio:

1. **PWA React** - Interface pública para usuários finais
2. **Painel Admin** - Interface administrativa com Laravel Blade

## Estrutura de URLs em Produção

```
https://operacaoalfa.com/
│
├── PWA React (SPA) - Interface Pública
│   ├── /                          → Página inicial
│   ├── /login                     → Login de usuários
│   ├── /register                  → Registro de usuários
│   ├── /simulados                 → Lista de simulados
│   ├── /simulados/:id             → Executar simulado
│   ├── /ranking                   → Ranking geral
│   ├── /carreiras                 → Lista de carreiras
│   ├── /carreiras/:id             → Detalhes da carreira
│   ├── /desempenho                → Estatísticas do usuário
│   ├── /aprovados                 → Lista de aprovados
│   ├── /assinar                   → Planos de assinatura
│   └── /conta                     → Configurações da conta
│
├── API REST - Backend
│   ├── /api/login                 → Autenticação
│   ├── /api/register              → Registro
│   ├── /api/logout                → Logout
│   ├── /api/me                    → Dados do usuário
│   ├── /api/careers               → Carreiras
│   ├── /api/exams                 → Simulados
│   ├── /api/attempts              → Tentativas de simulados
│   ├── /api/ranking               → Ranking
│   ├── /api/performance           → Desempenho
│   ├── /api/approved              → Aprovados
│   ├── /api/plans                 → Planos de assinatura
│   └── /api/notifications         → Notificações push
│
└── Painel Admin - Interface Administrativa
    ├── /admin/login               → Login do admin
    ├── /admin/dashboard           → Dashboard
    ├── /admin/users               → Gerenciar usuários
    ├── /admin/careers             → Gerenciar carreiras
    ├── /admin/notices             → Gerenciar editais
    └── /admin/exams               → Gerenciar simulados
```

## Separação de Responsabilidades

### PWA React (/)
- **Tecnologia**: React + TypeScript + Vite
- **Autenticação**: Laravel Sanctum (token no localStorage)
- **Público**: Usuários finais (candidatos)
- **Funcionalidades**:
  - Fazer simulados
  - Ver ranking
  - Acompanhar desempenho
  - Gerenciar assinatura
  - Receber notificações push

### Painel Admin (/admin/*)
- **Tecnologia**: Laravel Blade + Tailwind CSS
- **Autenticação**: Laravel Session (tradicional)
- **Público**: Administradores e consultores
- **Funcionalidades**:
  - Gerenciar usuários
  - Criar/editar carreiras
  - Criar/editar simulados
  - Criar/editar questões
  - Visualizar estatísticas

### API REST (/api/*)
- **Tecnologia**: Laravel
- **Autenticação**: Laravel Sanctum (Bearer token)
- **Público**: PWA React (consumidor)
- **Formato**: JSON

## Configuração de Rotas

### routes/web.php
```php
// Painel Admin (Blade)
Route::prefix('admin')->group(function () {
    // Rotas do admin...
});

// PWA React (catch-all)
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!admin|api).*$');
```

### routes/api.php
```php
// Rotas públicas
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Rotas protegidas
Route::middleware('auth:sanctum')->group(function () {
    // Rotas da API...
});
```

## Fluxo de Autenticação

### Usuários do PWA
1. Login via `/api/login`
2. Recebe token JWT
3. Armazena no localStorage
4. Envia em todas requisições: `Authorization: Bearer {token}`

### Administradores
1. Login via `/admin/login`
2. Sessão tradicional do Laravel
3. Cookie de sessão gerenciado pelo navegador

## Deployment

### Nginx Configuration
```nginx
server {
    listen 80;
    server_name operacaoalfa.com;
    root /var/www/laravel/public;

    # API e Admin - Laravel
    location ~ ^/(api|admin) {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PWA - React SPA
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

## Vantagens desta Arquitetura

1. **Separação Clara**: PWA e Admin são independentes
2. **SEO Friendly**: Admin não precisa ser indexado
3. **Performance**: PWA usa React Router (navegação instantânea)
4. **Segurança**: Autenticações separadas (token vs sessão)
5. **Manutenção**: Cada interface pode evoluir independentemente
6. **Escalabilidade**: API pode ser consumida por outros clientes (mobile app, etc)

## Desenvolvimento Local

```bash
# PWA React
http://localhost:8090/

# Painel Admin
http://localhost:8090/admin/

# API
http://localhost:8090/api/
```

## Produção

```bash
# PWA React
https://operacaoalfa.com/

# Painel Admin
https://operacaoalfa.com/admin/

# API
https://operacaoalfa.com/api/
```
