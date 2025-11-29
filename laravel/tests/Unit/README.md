# Testes Unitários do Módulo de Cotações

Este diretório contém os testes unitários para o módulo de Cotações. Os testes unitários focam em testar unidades individuais de código de forma isolada, como classes de serviço, modelos e outros componentes.

## Estrutura dos Testes

```
tests/Unit/
├── Services/
│   └── QuoteServiceTest.php    # Testes do serviço de cotações
└── Models/
    └── QuoteTest.php           # Testes do modelo Quote
```

## Como Executar os Testes

Execute todos os testes unitários do módulo:

```bash
php artisan test tests/Unit/
```

Ou execute um teste específico:

```bash
php artisan test tests/Unit/Services/QuoteServiceTest.php
```

## Cobertura de Testes

### 1. Testes do Modelo Quote

- **Atributos preenchíveis**
  - Verifica se os atributos em `$fillable` estão corretos
  - Testa os tipos de dados dos atributos

- **Relacionamentos**
  - Testa a relação com o modelo Client
  - Testa a relação com o modelo Aircraft
  - Verifica os relacionamentos com usuários (criado por, atualizado por)

- **Acessores e Mutadores**
  - Testa formatação de valores monetários
  - Verifica cálculos derivados
  - Testa formatação de datas

- **Escopos de Consulta**
  - Testa filtros comuns (por status, datas, etc.)
  - Verifica ordenação padrão

### 2. Testes do QuoteService

- **Cálculos**
  - Cálculo de totais
  - Aplicação de taxas e descontos
  - Cálculo de distâncias

- **Regras de Negócio**
  - Validação de datas
  - Verificação de disponibilidade
  - Aplicação de políticas de preço

- **Transações**
  - Teste de rollback em caso de falha
  - Verificação de consistência de dados

### 3. Testes de Validação

- **StoreQuoteRequest**
  - Validação de campos obrigatórios
  - Formato de dados
  - Regras de negócio específicas

- **UpdateQuoteRequest**
  - Validação condicional
  - Regras específicas para atualização
  - Verificação de permissões

## Mocking e Stubs

Os testes utilizam mocks para isolar as dependências:

```php
// Exemplo de mock para o repositório
$repository = $this->createMock(QuoteRepository::class);
$repository->method('find')->willReturn($quote);

$service = new QuoteService($repository);
$result = $service->find(1);
```

## Fixtures

Os dados de teste são criados usando factories:

```php
// Criar uma cotação de teste
$quote = Quote::factory()->create([
    'status' => 'draft',
    'total_amount' => 10000.00
]);
```

## Boas Práticas

1. **Testes independentes** - Cada teste deve poder ser executado isoladamente
2. **Nomes descritivos** - Use métodos de teste que descrevam o comportamento esperado
3. **Dados de teste realistas** - Use dados que representem casos reais
4. **Cobertura de casos de borda** - Inclua testes para validar entradas inesperadas
5. **Testes rápidos** - Mantenha os testes rápidos para permitir iteração rápida

## Cobertura de Código

Para verificar a cobertura de código dos testes unitários:

```bash
XDEBUG_MODE=coverage php artisan test --coverage-html=storage/coverage-unit --testsuite=Unit
```

Isso gerará um relatório em `storage/coverage-unit/index.html`.

## Depuração

Para depurar um teste específico, use:

```php
dd($variable); // Imprime e encerra a execução

dump($variable); // Imprime e continua a execução
```

Ou use o recurso de debugging do seu IDE configurando pontos de interrupção.

## Manutenção

Ao modificar o código do módulo, lembre-se de:

1. Atualizar os testes existentes, se necessário
2. Adicionar novos testes para novas funcionalidades
3. Manter a consistência dos dados de teste
4. Verificar a cobertura de código

## Recursos Úteis

- [Documentação do PHPUnit](https://phpunit.de/documentation.html)
- [Testes no Laravel](https://laravel.com/docs/testing)
- [Mocking com PHPUnit](https://phpunit.readthedocs.io/en/9.5/test-doubles.html)
- [Database Testing](https://laravel.com/docs/database-testing)
