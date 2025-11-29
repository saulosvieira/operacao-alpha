# Checklist de Limpeza do Projeto Base

## Arquivos e Diretórios para Remover

### Módulos Desnecessários
- [ ] `laravel/modules/Quotes/` - Módulo completo de cotações
- [ ] `laravel/temp_laravel/` - Diretório temporário

### PDFs de Exemplo
- [ ] `laravel/cotacao-teste-2025-09-02-00-21-52.pdf`
- [ ] `laravel/proposta-sales-modelo-2025-09-02-00-35-40.pdf`
- [ ] `laravel/teste-mpdf-2025-09-02-00-17-19.pdf`
- [ ] `laravel/test-pdf-template.php`

### Arquivos Temporários
- [ ] `laravel/Archive.zip`
- [ ] `laravel/todo-list`
- [ ] `laravel/supervisord.log`
- [ ] `laravel/supervisord.pid`

## Limpeza de Código

### Routes (laravel/routes/web.php)
- [ ] Remover rotas relacionadas a Quotes
- [ ] Manter apenas rotas de autenticação e dashboard
- [ ] Adicionar comentário para rotas futuras

### Controllers
- [ ] Verificar e remover controllers não utilizados em `app/Http/Controllers/`
- [ ] Manter apenas controllers base do Laravel

### Models
- [ ] Verificar e remover models não utilizados em `app/Models/`
- [ ] Manter apenas User model

### Views
- [ ] Limpar views não utilizadas em `resources/views/`
- [ ] Manter estrutura base do AdminLTE
- [ ] Verificar layouts e componentes necessários

### Migrations
- [ ] Verificar migrations existentes em `database/migrations/`
- [ ] Remover migrations relacionadas a Quotes
- [ ] Manter apenas migrations base do Laravel

### Services/Repositories
- [ ] Verificar `app/Services/` e remover não utilizados
- [ ] Verificar `app/Repositories/` e remover não utilizados

## Comandos de Limpeza

```bash
# Remover diretórios
rm -rf laravel/modules/Quotes
rm -rf laravel/temp_laravel

# Remover PDFs de exemplo
rm laravel/cotacao-teste-*.pdf
rm laravel/proposta-sales-*.pdf
rm laravel/teste-mpdf-*.pdf
rm laravel/test-pdf-template.php

# Remover arquivos temporários
rm laravel/Archive.zip
rm laravel/todo-list
rm laravel/supervisord.log
rm laravel/supervisord.pid

# Limpar cache do Laravel
docker exec simulados-app php artisan cache:clear
docker exec simulados-app php artisan config:clear
docker exec simulados-app php artisan route:clear
docker exec simulados-app php artisan view:clear
```

## Verificações Pós-Limpeza

- [ ] Testar se a aplicação sobe sem erros
- [ ] Verificar se AdminLTE está funcionando
- [ ] Testar autenticação (se existente)
- [ ] Verificar logs de erro
- [ ] Confirmar que não há rotas quebradas

## Estrutura Esperada Após Limpeza

```
laravel/
├── app/
│   ├── Console/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Controller.php (base)
│   │   └── Middleware/
│   ├── Models/
│   │   └── User.php
│   ├── Providers/
│   └── Services/ (vazio ou limpo)
├── config/
├── database/
│   ├── migrations/
│   │   └── (migrations base do Laravel)
│   └── seeders/
├── public/
├── resources/
│   ├── views/
│   │   ├── layouts/ (AdminLTE)
│   │   └── vendor/ (AdminLTE)
│   ├── css/
│   └── js/
├── routes/
│   ├── web.php (limpo)
│   └── api.php
├── storage/
├── tests/
├── .env
├── composer.json
└── package.json
```

## Próximos Passos Após Limpeza

1. Atualizar .env com novas configurações
2. Subir containers Docker
3. Executar migrations base
4. Criar primeira migration do projeto (carreiras)
5. Iniciar desenvolvimento do CRUD de carreiras
