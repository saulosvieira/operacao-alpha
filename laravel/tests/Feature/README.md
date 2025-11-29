# Testes de Feature do Módulo de Cotações

Este diretório contém os testes de feature para o módulo de Cotações. Os testes de feature simulam a interação do usuário com a aplicação, testando fluxos completos e a integração entre diferentes componentes.

## Estrutura dos Testes

```
tests/Feature/
└── QuoteControllerTest.php    # Testes do controlador web
```

## Como Executar os Testes

Execute todos os testes de feature do módulo:

```bash
php artisan test tests/Feature/QuoteControllerTest.php
```

Ou execute um teste específico:

```bash
php artisan test --filter=test_user_can_view_quotes_index
```

## Cobertura de Testes

### 1. Testes de Navegação

- **Listagem de Cotações**
  - Acesso autenticado
  - Paginação
  - Filtros e ordenação
  - Mensagens de lista vazia

- **Criação de Cotação**
  - Exibição do formulário
  - Submissão com sucesso
  - Validação de campos
  - Mensagens de erro

- **Visualização de Cotação**
  - Exibição de detalhes
  - Formatação de dados
  - Botões de ação condicionais

- **Edição de Cotação**
  - Carregamento do formulário
  - Atualização com sucesso
  - Validação
  - Histórico de alterações

- **Exclusão de Cotação**
  - Confirmação
  - Exclusão com sucesso
  - Restrições de permissão

### 2. Testes de Funcionalidade

- **Geração de PDF**
  - Download do arquivo
  - Formatação
  - Dados corretos

- **Envio por E-mail**
  - Formulário de envio
  - Validação de e-mail
  - Mensagem de confirmação

- **Aceitação/Rejeição**
  - Botões de ação
  - Confirmação
  - Atualização de status

### 3. Testes de Segurança

- **Controle de Acesso**
  - Redirecionamento para login
  - Acesso negado para não autorizados
  - Permissões específicas

- **Proteção CSRF**
  - Verificação de token
  - Proteção contra envios não autorizados

## Autenticação nos Testes

Os testes utilizam o método `actingAs()` para simular usuários autenticados:

```php
// Usuário comum
$user = User::factory()->create();
$this->actingAs($user);

// Usuário administrador
$admin = User::factory()->admin()->create();
$this->actingAs($admin);
```

## Testando Respostas

Exemplos de asserções comuns:

```php
// Verificar status da resposta
$response->assertStatus(200);

// Verificar se uma view foi carregada
$response->assertViewIs('quotes.index');

// Verificar se um dado está presente na view
$response->assertViewHas('quotes');

// Verificar redirecionamento
$response->assertRedirect(route('quotes.index'));

// Verificar mensagem de sessão
$response->assertSessionHas('success');

// Verificar se um arquivo foi baixado
$response->assertDownload();
```

## Dados de Teste

Os testes utilizam factories para criar dados consistentes:

```php
// Criar uma cotação de teste
$quote = Quote::factory()->create([
    'status' => 'draft',
    'total_amount' => 10000.00
]);

// Criar um usuário com permissões específicas
$user = User::factory()
    ->withPermissions(['quotes.view', 'quotes.create'])
    ->create();
```

## Testando Formulários

Para testar envio de formulários:

```php
// Teste de envio de formulário
$response = $this->post(route('quotes.store'), [
    'client_id' => $client->id,
    'aircraft_id' => $aircraft->id,
    // ... outros campos
]);

// Verificar redirecionamento após criação
$response->assertRedirect(route('quotes.show', $quote->id));

// Verificar se a cotação foi criada
$this->assertDatabaseHas('quotes', [
    'client_id' => $client->id,
    'status' => 'draft'
]);
```

## Testando Middleware

Para testar proteção de rotas:

```php
// Testar rota protegida sem autenticação
$response = $this->get(route('quotes.create'));
$response->assertRedirect(route('login'));

// Testar rota com usuário sem permissão
$user = User::factory()->create();
$response = $this->actingAs($user)
    ->get(route('quotes.create'));
$response->assertForbidden();
```

## Boas Práticas

1. **Um teste, um cenário** - Cada teste deve verificar um comportamento específico
2. **Nomes descritivos** - Use nomes que expliquem o que está sendo testado
3. **Dados independentes** - Cada teste deve configurar seus próprios dados
4. **Assertivas específicas** - Prefira asserções específicas sobre o comportamento esperado
5. **Código DRY** - Use métodos auxiliares para código repetitivo

## Depuração

Para depurar um teste específico, você pode usar:

```php
// Parar a execução e fazer dump do conteúdo
$this->get(route('quotes.index'))->dump();

// Verificar o conteúdo da sessão
dd(session()->all());

// Verificar erros de validação
dd($response->exception->validator->errors());
```

## Cobertura de Código

Para gerar um relatório de cobertura dos testes de feature:

```bash
XDEBUG_MODE=coverage php artisan test --coverage-html=storage/coverage-feature --testsuite=Feature
```

O relatório estará disponível em `storage/coverage-feature/index.html`.

## Manutenção

Ao modificar as views ou controladores, lembre-se de:

1. Atualizar os testes existentes, se necessário
2. Adicionar testes para novas funcionalidades
3. Verificar a cobertura de código
4. Manter a consistência dos testes com as regras de negócio

## Recursos Úteis

- [HTTP Tests no Laravel](https://laravel.com/docs/http-tests)
- [Testes de Autenticação](https://laravel.com/docs/authentication#authentication-testing)
- [Testes de Sessão](https://laravel.com/docs/session#session-testing)
- [Testes de Arquivos](https://laravel.com/docs/http-tests#testing-file-uploads)
