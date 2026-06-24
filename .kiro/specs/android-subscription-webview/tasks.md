# Implementation Plan: Fluxo de Assinaturas via WebView Protegida

## Overview

Implementação do fluxo completo de assinatura via WebView embarcada (WebView_Checkout) no app Flutter "Operação Alfa". O trabalho está dividido em: configuração de rotas e URL patterns, implementação do bridge e serviço de polling, tela da WebView com injeção de token e interceptação de navegação, modificações na Tela_Planos (assinar, cancelar, restaurar), invalidação de cache, e testes de propriedade. Cada task constrói incrementalmente sobre as anteriores, terminando com a integração final.

## Tasks

- [x] 1. Configurar rotas, constantes e URL patterns
  - [x] 1.1 Adicionar route path e URL patterns de checkout
    - Adicionar `static const String checkout = '/checkout';` e o método `checkoutPath(String checkoutUrl)` em `route_paths.dart`
    - Adicionar os patterns `kUrlCheckoutSuccessPattern`, `kUrlCheckoutCancelPattern`, `kEdduzHosts`, e as funções `isAllowedCheckoutHost`, `isCheckoutSuccessPath`, `isCheckoutCancelPath` em `url_patterns.dart`
    - _Requirements: 4.2, 4.3, 4.4, 4.5, 9.1_

  - [x] 1.2 Registrar a rota `/checkout` no GoRouter
    - Adicionar `GoRoute` fora do `ShellRoute` em `app_router.dart` com redirect para validação de `checkoutUrl` (esquema https, não vazio) e builder apontando para `WebViewCheckoutScreen`
    - O redirect deve redirecionar para `/planos` se URL inválida ou ausente
    - _Requirements: 9.1, 9.2, 9.3, 9.4, 7.1_

  - [x] 1.3 Write property test: Checkout URL Validation
    - **Property 1: Checkout URL Validation**
    - Gerar strings aleatórias e validar que somente URLs com esquema `https` e URI parseable são aceitas; todos os demais são rejeitados
    - **Validates: Requirements 1.2, 9.2, 9.3**

  - [x] 1.4 Write property test: Checkout URL Path Pattern Matching
    - **Property 4: Checkout URL Path Pattern Matching**
    - Gerar paths aleatórios e validar que `isCheckoutSuccessPath` e `isCheckoutCancelPath` correspondem mutuamente exclusivos aos padrões configurados
    - **Validates: Requirements 4.2, 4.3**

  - [x] 1.5 Write property test: Navigation Host Validation
    - **Property 5: Navigation Host Validation**
    - Gerar URIs com hosts aleatórios e validar que `isAllowedCheckoutHost` retorna true apenas para hosts do domínio sistema ou Edduz
    - **Validates: Requirements 4.4, 4.5**

- [x] 2. Implementar CheckoutBridge (JavascriptChannel)
  - [x] 2.1 Criar `checkout_bridge.dart` com eventos e implementação
    - Criar o arquivo em `lib/features/plans/services/checkout_bridge.dart`
    - Implementar `CheckoutBridgeEvent` sealed class, `CheckoutCompletedEvent`, `CheckoutCancelledEvent`, `CheckoutErrorEvent`
    - Implementar `CheckoutBridgeImpl` com parsing JSON, despacho por `type`, e log silencioso para mensagens inválidas
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6_

  - [x] 2.2 Write property test: CheckoutBridge Message Parsing
    - **Property 3: CheckoutBridge Message Parsing**
    - Gerar strings arbitrárias (JSON válido/inválido, tipos conhecidos/desconhecidos) e validar que o bridge despacha exatamente o evento correto ou nenhum evento sem lançar exceção
    - **Validates: Requirements 3.2, 3.3, 3.4, 3.5, 3.6**

- [x] 3. Implementar SubscriptionPollingService
  - [x] 3.1 Criar `subscription_polling_service.dart`
    - Criar o arquivo em `lib/features/plans/services/subscription_polling_service.dart`
    - Implementar `PollingResult` sealed class (`PollingSuccess`, `PollingTimeout`, `PollingPaymentFailed`)
    - Implementar `SubscriptionPollingServiceImpl` com Timer.periodic, contagem de tentativas, ignorar falhas pontuais, cancel/dispose
    - _Requirements: 5.1, 5.2, 5.3, 5.5, 5.6, 5.7_

  - [x] 3.2 Write property test: Polling Termination Conditions
    - **Property 7: Polling Termination Conditions**
    - Gerar sequências aleatórias de respostas (active, cancelled, inactive, network error) e validar que o polling termina com o resultado correto conforme as condições definidas
    - **Validates: Requirements 5.1, 5.2, 5.3, 5.5, 5.6**

- [x] 4. Implementar CheckoutController e CheckoutState
  - [x] 4.1 Criar `CheckoutState` com freezed e `CheckoutController` Riverpod AsyncNotifier
    - Criar `lib/features/plans/providers/checkout_controller.dart`
    - Implementar `CheckoutState` freezed (loading, active, confirming, success, timeout, error, cancelled)
    - Implementar `CheckoutController` com: `initialize()`, `onCheckoutCompleted()` com flag de deduplicação, `onCheckoutCancelled()`, `onCheckoutError()`, `retry()`, `checkStatusManually()`, `dispose()`
    - Integrar com `SubscriptionPollingService` e `CacheManager` (invalidação ao detectar status active)
    - _Requirements: 1.1, 1.2, 4.6, 4.7, 5.1, 5.2, 5.3, 5.6, 6.1_

  - [x] 4.2 Write property test: Checkout Completion Idempotency
    - **Property 6: Checkout Completion Idempotency**
    - Chamar `onCheckoutCompleted` N vezes (N ≥ 1) e validar que o polling é iniciado exatamente uma vez
    - **Validates: Requirements 4.7**

- [x] 5. Checkpoint - Verificar compilação e testes das camadas base
  - Ensure all tests pass, ask the user if questions arise.

- [x] 6. Implementar WebViewCheckoutScreen
  - [x] 6.1 Criar `webview_checkout_screen.dart` com injeção de token e carregamento
    - Criar `lib/features/plans/screens/webview_checkout_screen.dart`
    - Configurar `WebViewController` com JavaScript habilitado e DOM storage
    - Implementar injeção de token via `SessionManager.injectIntoWebView` com timeout de 5s antes do `loadRequest`
    - Implementar timeout de 30s para carregamento inicial com tela de erro
    - Registrar `CheckoutBridge` via `addJavaScriptChannel`
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 3.1, 3.7, 4.8_

  - [x] 6.2 Implementar NavigationDelegate para interceptação de URLs
    - Configurar `NavigationDelegate` na WebView
    - Interceptar URLs de sucesso (bloquear navegação + disparar `onCheckoutCompleted`)
    - Interceptar URLs de cancelamento (bloquear + retornar à Tela_Planos)
    - Permitir navegação livre para hosts do sistema e Edduz
    - Bloquear hosts externos e abrir via `url_launcher`
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

  - [x] 6.3 Implementar tratamento do botão voltar com PopScope
    - Implementar `PopScope` com verificação de histórico interno da WebView (`canGoBack`)
    - Se houver histórico: navegar para trás na WebView
    - Se não houver: exibir diálogo de confirmação "Abandonar checkout?"
    - Confirmar abandono → fechar WebView, retornar Tela_Planos
    - Cancelar abandono → manter WebView ativa
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_

  - [x] 6.4 Criar `confirming_payment_view.dart` (tela intermediária de polling)
    - Criar `lib/features/plans/widgets/confirming_payment_view.dart`
    - Exibir mensagem "Confirmando seu pagamento..." com indicador animado de progresso
    - Integrar com `CheckoutState.confirming` para exibir contagem de tentativas
    - Exibir botão "Verificar novamente" no estado timeout
    - _Requirements: 5.4, 5.3_

  - [x] 6.5 Write property test: Token Injection JS Escaping Round-Trip
    - **Property 2: Token Injection JS Escaping Round-Trip**
    - Gerar tokens com caracteres especiais (aspas, barras, unicode) e validar que o JS gerado preserva o valor original após execução
    - **Validates: Requirements 2.1, 2.5**

- [x] 7. Modificar PlansScreen e PlansController para checkout interno
  - [x] 7.1 Modificar `PlansController` — adicionar método `startCheckout`
    - Adicionar método `startCheckout(String planId)` que chama `POST /api/edduz/checkout` com timeout de 15s
    - Retornar `checkout_url` em caso de sucesso
    - Lançar `ValidationException` para HTTP 422, `UnauthorizedException` para 401
    - _Requirements: 1.1, 1.3, 1.5, 1.7_

  - [x] 7.2 Modificar `PlansScreen` — botão "Assinar" com navegação interna
    - Substituir chamada `url_launcher` por navegação para `/checkout?checkoutUrl=<encoded>`
    - Adicionar loading state no botão durante chamada API (desabilitar + spinner)
    - Tratar erros (422 → mensagem da API, rede/5xx → snackbar com retry, 401 → redirect automático)
    - _Requirements: 1.2, 1.3, 1.4, 1.6, 1.7_

  - [x] 7.3 Adicionar botão "Restaurar compra" na PlansScreen
    - Implementar botão visível independente do status da assinatura
    - Chamar `GET /api/subscription/status`, comparar com status local
    - Se diferente: atualizar, invalidar cache, exibir confirmação
    - Se igual: exibir "já sincronizado"
    - Tratar 401 (sessão expirada) e erros de rede com retry
    - Loading state no botão durante requisição
    - _Requirements: 11.1, 11.2, 11.3, 11.4, 11.5, 11.6, 11.7_

- [x] 8. Implementar cancelamento de assinatura
  - [x] 8.1 Criar `cancel_subscription_dialog.dart`
    - Criar `lib/features/plans/widgets/cancel_subscription_dialog.dart`
    - Exibir modal com data de expiração formatada (dd/MM/yyyy)
    - Botões "Confirmar cancelamento" (com loading) e "Manter assinatura"
    - Desabilitar ambos durante processamento
    - _Requirements: 10.2, 10.3, 10.6_

  - [x] 8.2 Integrar cancelamento na PlansScreen e PlansController
    - Adicionar botão "Cancelar assinatura" visível quando `subscription_status == active`
    - Ao confirmar: chamar `POST /api/subscription/cancel` com timeout 15s
    - Sucesso (200): atualizar status para "cancelled", invalidar cache, exibir mensagem com data
    - Erro (rede/5xx): fechar diálogo, exibir erro, manter estado
    - _Requirements: 10.1, 10.4, 10.5_

- [x] 9. Implementar invalidação de cache para mudança de assinatura
  - [x] 9.1 Expandir `invalidation_policy.dart` com patterns de planos
    - Adicionar `'plans:list'` ao array de `CacheInvalidationEvent.subscriptionChanged`
    - Garantir que o evento invalida atomicamente: `user:me`, `exams:list:*`, `plans:list`
    - _Requirements: 6.1_

  - [x] 9.2 Implementar recarregamento automático de telas visíveis após invalidação
    - Garantir que providers Riverpod de planos, exames e perfil reagem à invalidação
    - Se tela visível depende de dados invalidados: recarregar automaticamente
    - Se recarregamento falha: exibir dados antigos + indicador + retry
    - _Requirements: 6.2, 6.3, 6.4, 6.5, 6.6_

  - [x] 9.3 Write property test: Cache Invalidation Completeness
    - **Property 8: Cache Invalidation Completeness**
    - Invocar `applyInvalidation(subscriptionChanged)` e validar que exatamente as chaves `user:me`, `exams:list:*` e `plans:list` são invalidadas
    - **Validates: Requirements 6.1**

- [x] 10. Checkpoint - Verificar integração completa
  - Ensure all tests pass, ask the user if questions arise.

- [x] 11. Testes de integração e wiring final
  - [x] 11.1 Verificar fluxo completo de navegação e wiring
    - Validar que a navegação da Tela_Planos → /checkout → WebView → Polling → Tela_Planos funciona end-to-end
    - Validar que a Bottom_Navigation está oculta durante toda a permanência em `/checkout`
    - Validar que após retorno a Bottom_Navigation reaparece com aba correta
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 9.1, 9.2_

  - [x] 11.2 Write integration tests para fluxos principais
    - Teste de checkout completo (mock API): plans → checkout → polling → success → plans
    - Teste de cancelamento de assinatura: botão → diálogo → confirmar → cache invalidado
    - Teste de restauração de compra: botão → API → atualização
    - _Requirements: 1.1-1.8, 5.1-5.7, 10.1-10.6, 11.1-11.7_

- [x] 12. Final checkpoint - Validação completa
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marcadas com `*` são opcionais e podem ser ignoradas para um MVP mais rápido
- Cada task referencia requisitos específicos para rastreabilidade
- Checkpoints garantem validação incremental do progresso
- Property tests validam propriedades universais de corretude usando `glados`
- Unit tests validam cenários específicos e edge cases
- O padrão de implementação segue a WebView_Simulado já existente no projeto
- Todos os timeouts de API são 15s (exceto carregamento WebView: 30s e injeção de token: 5s)
- A flag `_checkoutCompleted` é crítica para deduplicação de eventos (URL + JS channel)

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1.1"] },
    { "id": 1, "tasks": ["1.2", "1.3", "1.4", "1.5", "2.1"] },
    { "id": 2, "tasks": ["2.2", "3.1"] },
    { "id": 3, "tasks": ["3.2", "4.1"] },
    { "id": 4, "tasks": ["4.2", "6.1"] },
    { "id": 5, "tasks": ["6.2", "6.3", "6.4", "6.5"] },
    { "id": 6, "tasks": ["7.1", "9.1"] },
    { "id": 7, "tasks": ["7.2", "7.3", "8.1"] },
    { "id": 8, "tasks": ["8.2", "9.2"] },
    { "id": 9, "tasks": ["9.3", "11.1"] },
    { "id": 10, "tasks": ["11.2"] }
  ]
}
```
