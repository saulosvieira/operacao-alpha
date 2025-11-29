# Laravel Base Template

Um template base Laravel com AdminLTE, gerenciamento de usuários e clientes.

## Funcionalidades

- ✅ Autenticação de usuários
- ✅ Gerenciamento de usuários (CRUD)
- ✅ Gerenciamento de clientes (CRUD)
- ✅ Interface AdminLTE responsiva
- ✅ Sistema de roles (admin/user)
- ✅ Componentes reutilizáveis (modais, tabelas, paginação)
- ✅ Helpers para formatação (CPF/CNPJ, telefone, moeda, datas)

## Requisitos

- PHP 8.2+
- Composer
- MySQL/PostgreSQL
- Node.js (para assets)

## Instalação

1. Clone o repositório:
```bash
git clone <repository-url>
cd laravel-base-template
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
DB_DATABASE=laravel_base
DB_USERNAME=root
DB_PASSWORD=
```

5. Execute as migrações e seeders:
```bash
php artisan migrate --seed
```

6. Compile os assets:
```bash
npm run build
```

7. Inicie o servidor:
```bash
php artisan serve
```

## Usuários Padrão

Após executar os seeders, você terá acesso aos seguintes usuários:

- **Admin**: admin@example.com / admin123
- **Usuário**: user@example.com / user123

## Estrutura

### Models
- `User` - Gerenciamento de usuários
- `Client` - Gerenciamento de clientes

### Controllers
- `AuthController` - Autenticação
- `UserController` - CRUD de usuários
- `ClientController` - CRUD de clientes
- `DashboardController` - Dashboard principal

### Services
- `AuthService` - Lógica de autenticação
- `UserService` - Lógica de usuários
- `ClientService` - Lógica de clientes

### Repositories
- `UserRepository` - Acesso a dados de usuários
- `ClientRepository` - Acesso a dados de clientes

### Helpers
- `FormatHelper` - Formatação de CPF/CNPJ, telefone, moeda, datas
- `TableHelper` - Helpers para ordenação de tabelas

## Componentes Blade

### Componentes Reutilizáveis
- `action-buttons` - Botões de ação (visualizar, editar, excluir)
- `data-table` - Tabela de dados com ordenação
- `delete-confirmation` - Modal de confirmação de exclusão
- `modal-simple` - Modal simples
- `pagination` - Paginação customizada

### Layout
- `layouts.voemtx` - Layout base com AdminLTE

## Rotas

### Autenticação
- `GET /login` - Formulário de login
- `POST /login` - Processar login
- `POST /logout` - Logout

### Dashboard
- `GET /dashboard` - Dashboard principal

### Usuários
- `GET /usuarios` - Listar usuários
- `GET /usuarios/create` - Formulário de criação
- `POST /usuarios` - Criar usuário
- `GET /usuarios/{user}` - Visualizar usuário
- `GET /usuarios/{user}/edit` - Formulário de edição
- `PUT /usuarios/{user}` - Atualizar usuário
- `DELETE /usuarios/{user}` - Excluir usuário

### Clientes
- `GET /clientes` - Listar clientes
- `GET /clientes/create` - Formulário de criação
- `POST /clientes` - Criar cliente
- `GET /clientes/{client}` - Visualizar cliente
- `GET /clientes/{client}/edit` - Formulário de edição
- `PUT /clientes/{client}` - Atualizar cliente
- `DELETE /clientes/{client}` - Excluir cliente

## Personalização

### Branding
Para personalizar o branding da aplicação:

1. Edite `config/adminlte.php` para alterar título e logo
2. Modifique `config/app.php` para alterar o nome da aplicação
3. Atualize os assets em `public/css/app.css`

### Adicionando Novos Módulos
Para adicionar novos módulos, siga o padrão:

1. Crie o Model em `app/Models/`
2. Crie o Repository em `app/Repositories/`
3. Crie o Service em `app/Services/`
4. Crie o Controller em `app/Http/Controllers/`
5. Crie as views em `resources/views/`
6. Adicione as rotas em `routes/web.php`
7. Atualize o menu em `config/adminlte.php`

## Licença

Este projeto está licenciado sob a [MIT License](LICENSE).