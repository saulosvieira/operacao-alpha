# Documento de Requisitos — Integração de Assinaturas com Edduz

## Introdução

Este documento descreve os requisitos para integrar o sistema de assinaturas existente com a plataforma Edduz. A integração permite que usuários realizem assinaturas via Edduz, que o sistema receba notificações de eventos de assinatura via webhook, e que administradores consultem o histórico de webhooks recebidos em uma página dedicada no painel admin.

## Glossário

- **Sistema**: A aplicação Laravel que gerencia assinaturas, exames e carreiras.
- **Edduz**: Plataforma externa de pagamentos e assinaturas utilizada para processar as assinaturas dos usuários.
- **Webhook_Edduz**: Endpoint HTTP do Sistema que recebe notificações (callbacks) enviadas pela Edduz sobre eventos de assinatura.
- **Payload_Webhook**: Corpo da requisição HTTP enviada pela Edduz ao Webhook_Edduz, contendo dados do evento de assinatura.
- **Registro_Webhook**: Registro persistido no banco de dados contendo o Payload_Webhook recebido, metadados da requisição e resultado do processamento.
- **Painel_Admin**: Interface web administrativa do Sistema, acessível apenas por usuários com papel de administrador.
- **Página_Histórico_Webhook**: Página dentro do Painel_Admin que exibe a lista de Registros_Webhook recebidos.
- **Checkout_Edduz**: Página ou fluxo de pagamento hospedado pela Edduz onde o usuário finaliza a assinatura.
- **Token_Webhook**: Chave secreta compartilhada entre a Edduz e o Sistema, utilizada para validar a autenticidade das requisições de webhook.
- **Usuário**: Pessoa cadastrada no Sistema que deseja realizar ou gerenciar uma assinatura.
- **Administrador**: Usuário com papel "admin" que tem acesso ao Painel_Admin.

## Requisitos

### Requisito 1: Iniciar Fluxo de Assinatura via Edduz

**User Story:** Como um Usuário, eu quero iniciar o processo de assinatura através da Edduz, para que eu possa pagar e ativar minha assinatura no Sistema.

#### Critérios de Aceitação

1. WHEN o Usuário seleciona um plano de assinatura pago, THE Sistema SHALL gerar uma URL de Checkout_Edduz contendo o identificador do plano e o identificador do Usuário.
2. WHEN o Sistema gera a URL de Checkout_Edduz, THE Sistema SHALL redirecionar o Usuário para a URL de Checkout_Edduz.
3. IF o Sistema falha ao gerar a URL de Checkout_Edduz, THEN THE Sistema SHALL retornar uma mensagem de erro descritiva ao Usuário e registrar o erro em log.
4. THE Sistema SHALL enviar o identificador do Usuário e o identificador do plano como parâmetros na requisição à API da Edduz.

### Requisito 2: Receber Webhook da Edduz

**User Story:** Como o Sistema, eu quero receber notificações da Edduz via webhook, para que eu possa atualizar o status das assinaturas dos Usuários automaticamente.

#### Critérios de Aceitação

1. THE Webhook_Edduz SHALL expor um endpoint HTTP POST acessível publicamente no caminho `/api/webhooks/edduz`.
2. WHEN o Webhook_Edduz recebe uma requisição, THE Webhook_Edduz SHALL validar a autenticidade da requisição utilizando o Token_Webhook.
3. IF o Token_Webhook da requisição recebida não corresponde ao Token_Webhook configurado no Sistema, THEN THE Webhook_Edduz SHALL retornar HTTP 401 e registrar a tentativa em log.
4. WHEN o Webhook_Edduz recebe um Payload_Webhook válido com evento de assinatura confirmada, THE Sistema SHALL atualizar o status da assinatura do Usuário correspondente para "active" e definir a data de expiração conforme o plano.
5. WHEN o Webhook_Edduz recebe um Payload_Webhook válido com evento de assinatura cancelada, THE Sistema SHALL atualizar o status da assinatura do Usuário correspondente para "inactive".
6. WHEN o Webhook_Edduz recebe um Payload_Webhook válido com evento de assinatura expirada, THE Sistema SHALL atualizar o status da assinatura do Usuário correspondente para "expired".
7. IF o Payload_Webhook contém um identificador de Usuário que não existe no Sistema, THEN THE Webhook_Edduz SHALL registrar o erro no Registro_Webhook e retornar HTTP 200 para evitar reenvios pela Edduz.
8. WHEN o Webhook_Edduz processa uma requisição com sucesso, THE Webhook_Edduz SHALL retornar HTTP 200.

### Requisito 3: Persistir Histórico de Webhooks

**User Story:** Como o Sistema, eu quero persistir todos os webhooks recebidos da Edduz, para que exista um registro auditável de todas as notificações processadas.

#### Critérios de Aceitação

1. WHEN o Webhook_Edduz recebe uma requisição, THE Sistema SHALL criar um Registro_Webhook contendo: Payload_Webhook completo, endereço IP de origem, cabeçalhos HTTP relevantes, data e hora de recebimento, e resultado do processamento (sucesso ou erro).
2. THE Sistema SHALL persistir o Registro_Webhook independentemente do resultado da validação do Token_Webhook.
3. THE Sistema SHALL persistir o Registro_Webhook antes de processar a lógica de negócio do evento.
4. IF o processamento do evento falha após a persistência do Registro_Webhook, THEN THE Sistema SHALL atualizar o Registro_Webhook com a mensagem de erro.

### Requisito 4: Página de Histórico de Webhooks no Admin

**User Story:** Como um Administrador, eu quero visualizar o histórico de webhooks recebidos da Edduz no Painel_Admin, para que eu possa consultar e auditar as informações de assinaturas dos Usuários.

#### Critérios de Aceitação

1. THE Página_Histórico_Webhook SHALL ser acessível apenas por Administradores autenticados no Painel_Admin.
2. THE Página_Histórico_Webhook SHALL exibir uma lista paginada de Registros_Webhook ordenados por data de recebimento decrescente.
3. THE Página_Histórico_Webhook SHALL exibir para cada Registro_Webhook: data e hora de recebimento, tipo de evento, identificador do Usuário associado, status do processamento (sucesso ou erro), e endereço IP de origem.
4. WHEN o Administrador clica em um Registro_Webhook na lista, THE Página_Histórico_Webhook SHALL exibir os detalhes completos do Registro_Webhook incluindo o Payload_Webhook completo e a mensagem de erro quando aplicável.
5. WHERE o Administrador utiliza o filtro por status de processamento, THE Página_Histórico_Webhook SHALL exibir apenas os Registros_Webhook que correspondem ao status selecionado.
6. WHERE o Administrador utiliza o filtro por tipo de evento, THE Página_Histórico_Webhook SHALL exibir apenas os Registros_Webhook que correspondem ao tipo de evento selecionado.
7. WHERE o Administrador utiliza o filtro por período de datas, THE Página_Histórico_Webhook SHALL exibir apenas os Registros_Webhook recebidos dentro do período selecionado.

### Requisito 5: Configuração da Integração com Edduz

**User Story:** Como um Administrador, eu quero que as credenciais da Edduz sejam configuráveis via variáveis de ambiente, para que a integração possa ser configurada sem alteração de código.

#### Critérios de Aceitação

1. THE Sistema SHALL ler a URL base da API da Edduz a partir da variável de ambiente `EDDUZ_API_URL`.
2. THE Sistema SHALL ler a chave de API da Edduz a partir da variável de ambiente `EDDUZ_API_KEY`.
3. THE Sistema SHALL ler o Token_Webhook a partir da variável de ambiente `EDDUZ_WEBHOOK_TOKEN`.
4. IF alguma variável de ambiente obrigatória da Edduz não estiver configurada, THEN THE Sistema SHALL registrar um erro em log durante a inicialização e desabilitar as funcionalidades de integração com a Edduz.

### Requisito 6: Idempotência do Webhook

**User Story:** Como o Sistema, eu quero que o processamento de webhooks seja idempotente, para que reenvios da Edduz não causem efeitos colaterais indesejados.

#### Critérios de Aceitação

1. WHEN o Webhook_Edduz recebe um Payload_Webhook com um identificador de transação já processado, THE Sistema SHALL registrar o Registro_Webhook como duplicado e retornar HTTP 200 sem reprocessar o evento.
2. THE Sistema SHALL utilizar o identificador de transação da Edduz como chave de idempotência.
