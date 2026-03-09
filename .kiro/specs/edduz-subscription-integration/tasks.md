# Plano de Implementação: Integração de Assinaturas com Edduz

## Visão Geral

Implementação incremental da integração com a plataforma Edduz, seguindo a arquitetura DDD existente. Cada tarefa constrói sobre a anterior, começando pela infraestrutura (configuração, modelos, enums) e progredindo até os controllers, rotas e views do admin.

## Tarefas

- [x] 1. Configuração e infraestrutura base
  - [x] 1.1 Adicionar configuração da Edduz em `config/services.php`
    - Adicionar o bloco `edduz` com `api_url`, `api_key` e `webhook_token` lidos de variáveis de ambiente
    - Adicionar as variáveis `EDDUZ_API_URL`, `EDDUZ_API_KEY` e `EDDUZ_WEBHOOK_TOKEN` no `.env.example`
    - _Requisitos: 5.1, 5.2, 5.3_

  - [x] 1.2 Criar enums `WebhookEventType` e `WebhookProcessingStatus`
    - Criar `app/Domain/Edduz/Enums/WebhookEventType.php` com os cases: `SUBSCRIPTION_CONFIRMED`, `SUBSCRIPTION_CANCELLED`, `SUBSCRIPTION_EXPIRED`
    - Criar `app/Domain/Edduz/Enums/WebhookProcessingStatus.php` com os cases: `SUCCESS`, `ERROR`, `DUPLICATE`, `INVALID_TOKEN`
    - _Requisitos: 2.4, 2.5, 2.6, 3.1_

  - [x] 1.3 Criar migration e model `EdduzWebhookLog`
    - Criar migration para tabela `edduz_webhook_logs` com todas as colunas e índices definidos no design (transaction_id unique, event_type, user_id, payload JSON, ip_address, headers JSON, processing_status, error_message, received_at, processed_at)
    - Criar `app/Domain/Edduz/Models/EdduzWebhookLog.php` com fillable, casts e relação `user()`
    - _Requisitos: 3.1, 6.2_

  - [x] 1.4 Criar DTO `ProcessWebhookResult`
    - Criar `app/Domain/Edduz/DTOs/ProcessWebhookResult.php` com propriedades readonly: `httpStatus`, `message`, `processingStatus`
    - _Requisitos: 2.8_

- [x] 2. Camada de repositório e serviço API
  - [x] 2.1 Criar `EdduzWebhookLogRepository`
    - Criar `app/Domain/Edduz/Repositories/EdduzWebhookLogRepository.php`
    - Implementar métodos: `create()`, `updateProcessingResult()`, `findByTransactionId()`, `paginate()` com filtros (status, event_type, período), `findOrFail()`
    - _Requisitos: 3.1, 3.4, 4.2, 4.5, 4.6, 4.7, 6.1_

  - [x] 2.2 Criar `EdduzApiClient`
    - Criar `app/Services/EdduzApiClient.php`
    - Implementar `createCheckoutSession(string $userId, string $planId): string` usando Http facade do Laravel
    - Implementar `isConfigured(): bool` que verifica se todas as variáveis de ambiente obrigatórias estão presentes
    - Registrar o service no `AppServiceProvider` com injeção de configuração via `config('services.edduz')`
    - _Requisitos: 1.1, 1.4, 5.1, 5.2, 5.3, 5.4_

  - [x] 2.3 Escrever teste de propriedade para configuração incompleta
    - **Propriedade 10: Configuração incompleta desabilita integração**
    - **Valida: Requisito 5.4**

- [ ] 3. Checkpoint — Verificar infraestrutura base
  - Garantir que a migration roda corretamente, que os enums e o model estão funcionais, e que o `EdduzApiClient::isConfigured()` retorna corretamente. Rodar todos os testes existentes. Perguntar ao usuário se há dúvidas.

- [x] 4. Actions do domínio Edduz
  - [x] 4.1 Criar `GenerateCheckoutUrlAction`
    - Criar `app/Domain/Edduz/Actions/GenerateCheckoutUrlAction.php`
    - Injetar `EdduzApiClient` e `SubscriptionRepository`
    - Implementar `execute(string $userId, string $planId): string` que valida o plano (rejeita plano gratuito), chama `EdduzApiClient::createCheckoutSession()` e retorna a URL
    - Tratar exceções da API e registrar em log
    - _Requisitos: 1.1, 1.3, 1.4_

  - [x] 4.2 Escrever teste de propriedade para parâmetros do checkout
    - **Propriedade 1: Parâmetros do checkout contêm identificadores corretos**
    - **Valida: Requisitos 1.1, 1.4**

  - [x] 4.3 Criar `ProcessWebhookAction`
    - Criar `app/Domain/Edduz/Actions/ProcessWebhookAction.php`
    - Injetar `EdduzWebhookLogRepository` e `SubscriptionRepository`
    - Implementar `execute(array $payload, string $ipAddress, array $headers, string $receivedToken): ProcessWebhookResult`
    - Fluxo: persistir registro → validar token → verificar idempotência (transaction_id) → mapear evento para status de assinatura → atualizar assinatura → atualizar registro com resultado
    - Tratar cenários: token inválido (401), usuário não encontrado (200 com erro), transaction_id duplicado (200 como duplicate), evento desconhecido (200 com erro)
    - _Requisitos: 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8, 3.1, 3.2, 3.3, 3.4, 6.1, 6.2_

  - [x] 4.4 Escrever testes de propriedade para ProcessWebhookAction
    - **Propriedade 2: Token inválido retorna 401 e é registrado** — Valida: Requisito 2.3
    - **Propriedade 3: Mapeamento evento → status de assinatura** — Valida: Requisitos 2.4, 2.5, 2.6, 2.8
    - **Propriedade 4: Webhook é sempre registrado independentemente do resultado** — Valida: Requisitos 3.1, 3.2, 3.3
    - **Propriedade 5: Falha no processamento atualiza registro com erro** — Valida: Requisito 3.4
    - **Propriedade 11: Idempotência por transaction_id** — Valida: Requisitos 6.1, 6.2

- [ ] 5. Checkpoint — Verificar lógica de domínio
  - Garantir que as actions funcionam corretamente com testes. Rodar todos os testes. Perguntar ao usuário se há dúvidas.

- [x] 6. Controllers e rotas da API
  - [x] 6.1 Criar `EdduzCheckoutController`
    - Criar `app/Http/Controllers/Api/EdduzCheckoutController.php`
    - Implementar `checkout(Request $request, GenerateCheckoutUrlAction $action): JsonResponse`
    - Validar request (plan_id obrigatório), chamar action, retornar URL de checkout ou erro
    - _Requisitos: 1.1, 1.2, 1.3_

  - [x] 6.2 Criar `EdduzWebhookController`
    - Criar `app/Http/Controllers/Api/EdduzWebhookController.php`
    - Implementar `handle(Request $request, ProcessWebhookAction $action): JsonResponse`
    - Extrair payload, IP, headers e token da request, delegar para action, retornar resposta HTTP conforme resultado
    - Endpoint público (sem middleware de autenticação)
    - _Requisitos: 2.1, 2.8_

  - [x] 6.3 Registrar rotas da API
    - Criar `laravel/routes/api/edduz.php` com as rotas: `POST /edduz/checkout` (auth:sanctum) e `POST /webhooks/edduz` (público)
    - Importar o arquivo em `laravel/routes/api.php`
    - _Requisitos: 1.2, 2.1_

- [x] 7. Página de histórico de webhooks no admin
  - [x] 7.1 Criar `WebhookHistoryController`
    - Criar `app/Http/Controllers/Admin/WebhookHistoryController.php`
    - Implementar `index(Request $request, EdduzWebhookLogRepository $repo): View` com filtros (status, event_type, data início/fim) e paginação
    - Implementar `show(int $id, EdduzWebhookLogRepository $repo): View` para detalhes completos do registro
    - _Requisitos: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7_

  - [x] 7.2 Criar views Blade para histórico de webhooks
    - Criar view `resources/views/admin/webhooks/edduz/index.blade.php` com tabela paginada (data/hora, tipo evento, user_id, status, IP), filtros por status, tipo de evento e período de datas
    - Criar view `resources/views/admin/webhooks/edduz/show.blade.php` com detalhes completos incluindo payload JSON e mensagem de erro
    - Seguir o padrão de layout e componentes Blade já existentes no projeto
    - _Requisitos: 4.2, 4.3, 4.4, 4.5, 4.6, 4.7_

  - [x] 7.3 Registrar rotas do admin para webhooks
    - Adicionar rotas `GET /admin/webhooks/edduz` e `GET /admin/webhooks/edduz/{id}` dentro do grupo autenticado em `routes/web.php`
    - _Requisitos: 4.1_

  - [x] 7.4 Escrever testes de propriedade para página admin
    - **Propriedade 6: Acesso à página de histórico restrito a administradores** — Valida: Requisito 4.1
    - **Propriedade 7: Ordenação decrescente por data de recebimento** — Valida: Requisito 4.2
    - **Propriedade 9: Filtros retornam apenas registros correspondentes** — Valida: Requisitos 4.5, 4.6, 4.7

  - [x] 7.5 Escrever teste de propriedade para campos obrigatórios na listagem
    - **Propriedade 8: Listagem exibe todos os campos obrigatórios**
    - **Valida: Requisito 4.3**

- [ ] 8. Checkpoint final — Validação completa
  - Rodar todos os testes (existentes e novos). Verificar que todas as rotas estão registradas corretamente. Verificar que a migration roda sem erros. Perguntar ao usuário se há dúvidas.

## Notas

- Tarefas marcadas com `*` são opcionais e podem ser puladas para um MVP mais rápido
- Cada tarefa referencia requisitos específicos para rastreabilidade
- Checkpoints garantem validação incremental
- Testes de propriedade validam propriedades universais de corretude
- Testes unitários validam exemplos específicos e edge cases
