# Plano de Implementação: Material de Apoio da Questão

## Visão Geral

Implementação incremental dos campos opcionais de material de apoio (`support_text` e `support_pdf_url`) no modelo `Question`. Começa pela migração e model, depois DTOs e validação, importação Excel, views Blade do admin, API resource e frontend React. Cada tarefa constrói sobre a anterior.

## Tarefas

- [x] 1. Migração e atualização do modelo Question
  - [x] 1.1 Criar migração `add_support_fields_to_questions_table`
    - Adicionar coluna `support_text` do tipo `text`, nullable, após `explanation`
    - Adicionar coluna `support_pdf_url` do tipo `string(500)`, nullable, após `support_text`
    - _Requisitos: 1.1, 1.2, 1.3, 1.4_

  - [x] 1.2 Atualizar o model `Question` com os novos campos
    - Adicionar `'support_text'` e `'support_pdf_url'` ao array `$fillable` em `app/Domain/Exam/Models/Question.php`
    - _Requisitos: 1.1, 1.2_

- [x] 2. DTOs e validação do formulário admin
  - [x] 2.1 Atualizar `QuestionFormData` DTO com os novos campos
    - Adicionar propriedades `?string $supportText` e `?string $supportPdfUrl` ao construtor em `app/Domain/Exam/DTOs/Admin/QuestionFormData.php`
    - Atualizar `fromRequest()` para extrair `support_text` e `support_pdf_url` do request
    - Atualizar `toArray()` para incluir `'support_text'` e `'support_pdf_url'`
    - _Requisitos: 2.4, 2.5, 2.7_

  - [x] 2.2 Atualizar `QuestionData` DTO com os novos campos
    - Adicionar propriedades `?string $supportText` e `?string $supportPdfUrl` ao construtor em `app/Domain/Exam/DTOs/QuestionData.php`
    - Atualizar `fromArray()` para extrair os novos campos
    - Atualizar `toArray()` para incluir `'supportText'` e `'supportPdfUrl'`
    - _Requisitos: 1.1, 1.2_

  - [x] 2.3 Adicionar regras de validação nos FormRequests
    - Adicionar `'support_text' => 'nullable|string'` e `'support_pdf_url' => 'nullable|url|max:500'` em `StoreQuestionRequest`
    - Adicionar as mesmas regras em `UpdateQuestionRequest`
    - Adicionar mensagens de erro customizadas: `'support_pdf_url.url' => 'A URL informada para o PDF de apoio é inválida.'`
    - _Requisitos: 2.6, 2.7_

  - [ ]* 2.4 Escrever teste de propriedade para round-trip dos campos via formulário admin
    - **Propriedade 1: Round-trip dos campos de material de apoio via formulário admin**
    - **Valida: Requisitos 1.1, 1.2, 1.3, 1.4, 2.4, 2.5, 2.7**

  - [ ]* 2.5 Escrever teste de propriedade para rejeição de URL inválida
    - **Propriedade 2: Rejeição de URL inválida no formulário admin**
    - **Valida: Requisito 2.6**

- [x] 3. Checkpoint — Verificar modelo, DTOs e validação
  - Garantir que a migration roda corretamente, que o model tem os campos no fillable, que os DTOs extraem e serializam os novos campos, e que a validação rejeita URLs inválidas. Rodar todos os testes existentes. Perguntar ao usuário se há dúvidas.

- [x] 4. Importação Excel — Mapeamento e persistência
  - [x] 4.1 Adicionar mapeamento de colunas no `ExcelQuestionImport`
    - Adicionar `'texto_apoio' => 'support_text'` e `'link_pdf_apoio' => 'support_pdf_url'` ao array `$columnMapping` em `app/Domain/Import/Imports/ExcelQuestionImport.php`
    - _Requisitos: 3.1, 3.2, 3.3_

  - [x] 4.2 Atualizar `QuestionImportService::processQuestion()` para persistir os novos campos
    - Adicionar `'support_text'` e `'support_pdf_url'` ao array de `Question::create()` no método `processQuestion()` em `app/Domain/Import/Services/QuestionImportService.php`
    - Validar que `support_pdf_url` é uma URL válida quando preenchida; se inválida, setar como `null` e registrar aviso no relatório
    - _Requisitos: 3.2, 3.3, 3.4, 3.5_

  - [ ]* 4.3 Escrever teste de propriedade para round-trip dos campos via importação Excel
    - **Propriedade 3: Round-trip dos campos de material de apoio via importação Excel**
    - **Valida: Requisitos 3.2, 3.3, 3.4**

  - [ ]* 4.4 Escrever teste de propriedade para URL inválida na importação
    - **Propriedade 4: URL inválida na importação não impede a importação da questão**
    - **Valida: Requisito 3.5**

- [x] 5. Views Blade do admin — Formulários e tabela de importação
  - [x] 5.1 Adicionar campos de material de apoio no formulário de criação
    - Adicionar campo textarea "Texto de Apoio" (`name="support_text"`, opcional) após o campo "Explicação" em `resources/views/admin/questions/create.blade.php`
    - Adicionar campo input text "Link do PDF de Apoio" (`name="support_pdf_url"`, opcional) com `@error` para validação
    - Seguir o padrão AdminLTE existente nos outros campos do formulário
    - _Requisitos: 2.1, 2.2, 2.7_

  - [x] 5.2 Adicionar campos de material de apoio no formulário de edição
    - Adicionar os mesmos campos em `resources/views/admin/questions/edit.blade.php`, preenchidos com `{{ old('support_text', $question->support_text) }}` e `{{ old('support_pdf_url', $question->support_pdf_url) }}`
    - _Requisitos: 2.3, 2.7_

  - [x] 5.3 Atualizar tabela de formato esperado na tela de importação
    - Adicionar colunas `texto_apoio` e `link_pdf_apoio` na tabela de formato esperado em `resources/views/admin/questions/import/index.blade.php`
    - Adicionar valores de exemplo na linha de dados da tabela
    - _Requisitos: 3.6_

- [x] 6. Checkpoint — Verificar admin completo
  - Garantir que os formulários de criação e edição exibem os novos campos, que a validação funciona, que a importação Excel mapeia e persiste os campos, e que a tabela de formato esperado mostra as novas colunas. Rodar todos os testes. Perguntar ao usuário se há dúvidas.

- [x] 7. API e frontend React
  - [x] 7.1 Atualizar `QuestionResource` para incluir os novos campos na API
    - Adicionar `'supportText' => $this->resource->support_text` e `'supportPdfUrl' => $this->resource->support_pdf_url` ao array retornado por `toArray()` em `app/Http/Resources/Exam/QuestionResource.php`
    - _Requisitos: 6.1, 6.2, 6.3_

  - [ ]* 7.2 Escrever teste de propriedade para API sempre incluir campos de material de apoio
    - **Propriedade 6: API sempre inclui campos de material de apoio no payload**
    - **Valida: Requisitos 6.1, 6.2, 6.3**

  - [x] 7.3 Atualizar tipo `Question` no TypeScript
    - Adicionar `supportText?: string | null` e `supportPdfUrl?: string | null` à interface `Question` em `resources/react/types/index.ts`
    - Atualizar também a interface `Question` em `resources/react/services/exams.ts` se existir duplicada
    - _Requisitos: 6.1_

  - [x] 7.4 Criar componente `SupportTextModal`
    - Criar `resources/react/components/SupportTextModal.tsx` com um dialog/modal que recebe `text: string` e `isOpen: boolean` e `onClose: () => void`
    - Exibir o texto de apoio completo com formatação legível e botão de fechar
    - _Requisitos: 4.2, 4.3, 4.4_

  - [x] 7.5 Atualizar `QuestaoCard` com botões de material de apoio
    - Adicionar botão "Material de Apoio" visível condicionalmente quando `question.supportText` não é null, que abre o `SupportTextModal`
    - Adicionar botão "Baixar PDF" visível condicionalmente quando `question.supportPdfUrl` não é null, que abre a URL em nova aba via `window.open(url, '_blank')`
    - Posicionar os botões no header do card, ao lado do número da questão
    - _Requisitos: 4.1, 4.5, 5.1, 5.2, 5.3_

  - [ ]* 7.6 Escrever teste de propriedade para renderização condicional dos botões
    - **Propriedade 5: Renderização condicional dos botões de material de apoio**
    - **Valida: Requisitos 4.1, 4.5, 5.1, 5.3**

- [x] 8. Checkpoint final — Validação completa
  - Rodar todos os testes (existentes e novos). Verificar que a migration roda sem erros. Verificar fluxo completo: criar questão com material de apoio via admin → verificar na API → verificar no QuestaoCard. Verificar importação Excel com as novas colunas. Perguntar ao usuário se há dúvidas.

## Notas

- Tarefas marcadas com `*` são opcionais e podem ser puladas para um MVP mais rápido
- Cada tarefa referencia requisitos específicos para rastreabilidade
- Checkpoints garantem validação incremental
- Testes de propriedade validam propriedades universais de corretude
- O projeto usa Pest PHP sobre PHPUnit com banco SQLite em memória para testes
- Views admin seguem o padrão AdminLTE com Blade templates já estabelecido no projeto
- Frontend React usa componentes shadcn/ui (Button, Dialog) já disponíveis no projeto
