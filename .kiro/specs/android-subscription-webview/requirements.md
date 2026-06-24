# Requirements Document

## Introduction

Este documento define os requisitos para implementar o fluxo completo de assinaturas no aplicativo Android "Operação Alfa" utilizando uma WebView protegida dedicada (WebView_Checkout). O checkout da plataforma Edduz será aberto dentro do app — não no navegador externo — com token de autenticação injetado, interceptação de URLs para detectar conclusão/cancelamento, comunicação bidirecional via JavascriptChannel e detecção automática de mudança de status da assinatura. O padrão segue a mesma abordagem já implementada para a WebView_Simulado (Requisito 6 do flutter-hybrid-app), adaptada ao contexto de pagamento.

## Glossary

- **App_Flutter**: O aplicativo Flutter híbrido "Operação Alfa" que combina telas nativas com WebView
- **WebView_Checkout**: Componente WebView embutido no Flutter dedicado ao fluxo de checkout de assinaturas na plataforma Edduz, distinto da WebView_Simulado
- **Checkout_Edduz**: Página de pagamento hospedada pela Edduz onde o usuário finaliza a assinatura
- **Tela_Planos**: Tela nativa Flutter que lista os planos de assinatura disponíveis (já existente, definida no Requisito 19 do flutter-hybrid-app)
- **API_Laravel**: O backend Laravel existente que fornece endpoints REST autenticados via Laravel Sanctum
- **Sanctum_Bearer_Token**: Token opaco de autenticação persistido em armazenamento seguro e injetado na WebView via localStorage
- **Gerenciador_De_Sessão**: Componente que persiste o token em armazenamento seguro e o sincroniza com WebViews via injeção de localStorage
- **JavascriptChannel_App**: Canal de comunicação nativo exposto ao JavaScript da WebView_Checkout com nome "OperacaoAlfaApp", idêntico ao usado na WebView_Simulado
- **URL_Checkout_Sucesso**: URL para a qual a Edduz redireciona após pagamento confirmado (pattern configurável, ex: `*/checkout/success*` ou `*/obrigado*`)
- **URL_Checkout_Cancelamento**: URL para a qual a Edduz redireciona quando o usuário cancela o checkout (pattern configurável, ex: `*/checkout/cancel*`)
- **Polling_Status**: Mecanismo que consulta periodicamente GET /api/subscription/status para detectar mudança no status da assinatura após checkout
- **CacheManager**: Componente de cache local que invalida entradas por eventos (mudança de assinatura invalida GET /api/me e GET /api/exams)
- **Router_App**: Sistema de navegação do Flutter que gerencia rotas entre telas nativas e WebViews
- **Bottom_Navigation**: Barra de navegação inferior com 4 seções (Dashboard, Simulados, Ranking, Perfil)
- **Domínio_Sistema**: Hosts oficiais do sistema — `operacaoalfa.com.br` (produção) e `operacao-alfa.homolog.mydev.com.br` (homologação)

## Requirements

### Requisito 1: Obtenção da URL de Checkout e Abertura na WebView_Checkout

**User Story:** Como usuário, eu quero que ao selecionar um plano pago na Tela_Planos e tocar em "Assinar", o checkout da Edduz seja aberto dentro do app em uma WebView protegida, para que eu possa concluir o pagamento sem sair do aplicativo.

#### Critérios de Aceitação

1. WHEN o usuário seleciona um plano pago na Tela_Planos e toca em "Assinar", THE App_Flutter SHALL chamar POST /api/edduz/checkout enviando o identificador do plano selecionado e obter a URL de Checkout_Edduz retornada no campo `checkout_url` da resposta JSON
2. WHEN a URL de Checkout_Edduz é obtida com sucesso (HTTP 200) e possui esquema `https`, THE Router_App SHALL navegar para a tela contendo a WebView_Checkout e iniciar o carregamento da URL obtida; IF a URL retornada não possui esquema `https`, THEN THE App_Flutter SHALL tratar como erro e exibir mensagem informando que não foi possível iniciar o checkout
3. IF a chamada POST /api/edduz/checkout retorna HTTP 422 com erros de validação, THEN THE App_Flutter SHALL exibir a mensagem de erro retornada pela API ao usuário na Tela_Planos
4. IF a chamada POST /api/edduz/checkout falha por erro de rede ou a API_Laravel não responde dentro de 15 segundos, THEN THE App_Flutter SHALL exibir mensagem informando que não foi possível iniciar o checkout e oferecer botão para tentar novamente
5. IF a chamada POST /api/edduz/checkout retorna HTTP 401, THEN THE Gerenciador_De_Sessão SHALL encerrar a sessão e redirecionar o usuário para a tela de login conforme o fluxo de sessão expirada
6. WHILE a chamada POST /api/edduz/checkout está em andamento, THE Tela_Planos SHALL desabilitar o botão "Assinar" e exibir um indicador de carregamento até que a resposta seja recebida ou o timeout de 15 segundos seja atingido
7. IF a chamada POST /api/edduz/checkout retorna um código HTTP inesperado (diferente de 200, 401, 422), THEN THE App_Flutter SHALL exibir mensagem genérica de erro informando que o serviço está temporariamente indisponível e oferecer botão para tentar novamente
8. IF a WebView_Checkout falha ao carregar a URL de Checkout_Edduz após navegação bem-sucedida para a rota `/checkout`, THEN THE App_Flutter SHALL exibir mensagem de erro com opções de tentar novamente ou voltar à Tela_Planos conforme Requisito 4 critério 8

### Requisito 2: Injeção de Token de Autenticação na WebView_Checkout

**User Story:** Como usuário, eu quero que minha sessão de login seja automaticamente reconhecida na WebView de checkout, para que eu não precise fazer login novamente durante o pagamento.

#### Critérios de Aceitação

1. WHEN a WebView_Checkout é aberta, THE Gerenciador_De_Sessão SHALL injetar o Sanctum_Bearer_Token no localStorage da WebView na chave `auth_token` executando JavaScript do tipo `window.localStorage.setItem('auth_token', '<token>')`, e somente após a conclusão bem-sucedida da injeção iniciar o loadUrl da URL de Checkout_Edduz
2. THE WebView_Checkout SHALL habilitar JavaScript e armazenamento DOM (localStorage/sessionStorage) para funcionamento completo do Checkout_Edduz
3. WHILE a URL carregada na WebView_Checkout pertencer ao Domínio_Sistema, THE WebView_Checkout SHALL manter cookies e sessionStorage acessíveis ao contexto da página, permitindo que o Checkout_Edduz utilize a sessão autenticada
4. IF o Sanctum_Bearer_Token não está disponível no momento de abertura da WebView_Checkout, THEN THE App_Flutter SHALL abortar a abertura da WebView_Checkout, exibir mensagem informando que a sessão expirou e redirecionar o usuário para a tela de login
5. IF a execução do JavaScript de injeção do token falha ou não completa dentro de 5 segundos, THEN THE App_Flutter SHALL abortar o carregamento da WebView_Checkout, exibir mensagem de erro ao usuário e oferecer opção de tentar novamente

### Requisito 3: Comunicação Bidirecional entre Flutter e WebView_Checkout

**User Story:** Como desenvolvedor, eu quero que a WebView_Checkout possa enviar eventos ao app nativo e que o app possa executar JavaScript na WebView, para que haja integração completa entre os dois contextos.

#### Critérios de Aceitação

1. THE WebView_Checkout SHALL expor ao JavaScript da página um canal nativo via JavascriptChannel com o nome "OperacaoAlfaApp", seguindo o mesmo padrão da WebView_Simulado
2. THE JavascriptChannel_App SHALL aceitar mensagens estruturadas em JSON com pelo menos os tipos: `checkoutCompleted` (com campo `transactionId` quando disponível), `checkoutCancelled` e `checkoutError` (com campo `errorMessage`)
3. WHEN o JavaScript da página de checkout envia mensagem via `OperacaoAlfaApp.postMessage(JSON.stringify({type: 'checkoutCompleted'}))`, THE App_Flutter SHALL iniciar o fluxo de confirmação de assinatura definido no Requisito 5
4. WHEN o JavaScript da página de checkout envia mensagem via `OperacaoAlfaApp.postMessage(JSON.stringify({type: 'checkoutCancelled'}))`, THE App_Flutter SHALL fechar a WebView_Checkout e retornar o usuário à Tela_Planos
5. WHEN o JavaScript da página de checkout envia mensagem via `OperacaoAlfaApp.postMessage(JSON.stringify({type: 'checkoutError', errorMessage: '...'}))`, THE App_Flutter SHALL exibir a mensagem de erro ao usuário e oferecer opções de tentar novamente ou voltar à Tela_Planos
6. IF o JavascriptChannel_App recebe uma mensagem com JSON malformado ou com campo `type` não reconhecido, THEN THE App_Flutter SHALL ignorar a mensagem silenciosamente sem interromper o fluxo de checkout e registrar o evento em log de debug
7. THE App_Flutter SHALL ser capaz de executar JavaScript na WebView_Checkout via método `runJavaScript` para injeção de dados ou comunicação app-para-web quando necessário

### Requisito 4: Interceptação de Navegação e Detecção de Conclusão do Checkout

**User Story:** Como usuário, eu quero que o app detecte automaticamente quando meu pagamento foi concluído ou cancelado, para que eu seja redirecionado de volta à tela correta sem ação manual.

#### Critérios de Aceitação

1. THE WebView_Checkout SHALL utilizar NavigationDelegate para interceptar todas as requisições de navegação durante o fluxo de checkout
2. WHEN a WebView_Checkout navega para uma URL cujo path corresponde ao URL_Checkout_Sucesso configurado, THE App_Flutter SHALL bloquear o carregamento da página de sucesso no WebView e iniciar o fluxo de confirmação de assinatura definido no Requisito 5 em até 500 milissegundos após a interceptação
3. WHEN a WebView_Checkout navega para uma URL cujo path corresponde ao URL_Checkout_Cancelamento configurado, THE App_Flutter SHALL bloquear a navegação, fechar a WebView_Checkout e retornar o usuário à Tela_Planos exibindo uma mensagem indicando que o checkout foi cancelado pelo usuário
4. WHILE a WebView_Checkout está ativa, THE WebView_Checkout SHALL permitir navegação livre para qualquer URL cujo host pertence ao Checkout_Edduz ou ao Domínio_Sistema sem bloqueio pelo NavigationDelegate
5. IF a WebView_Checkout tenta navegar para uma URL cujo host não pertence ao Checkout_Edduz nem ao Domínio_Sistema, THEN THE App_Flutter SHALL bloquear a navegação na WebView e abrir a URL no navegador externo do dispositivo via url_launcher
6. WHEN o JavascriptChannel_App recebe a mensagem `checkoutCompleted`, THE App_Flutter SHALL iniciar o fluxo de confirmação de assinatura definido no Requisito 5, tratando este mecanismo como equivalente à interceptação de URL_Checkout_Sucesso descrita no critério 2
7. IF ambos os mecanismos de detecção (mensagem `checkoutCompleted` e navegação para URL_Checkout_Sucesso) disparam para a mesma sessão de checkout, THEN THE App_Flutter SHALL processar apenas o primeiro evento recebido e ignorar o segundo sem efeito colateral
8. IF a WebView_Checkout falha ao carregar a URL de Checkout_Edduz dentro de 30 segundos ou recebe erro de rede (conexão recusada, timeout DNS, ou HTTP 5xx) no carregamento inicial, THEN THE App_Flutter SHALL exibir mensagem de erro indicando falha no carregamento com duas opções de ação: tentar novamente (recarregar a URL) ou voltar à Tela_Planos

### Requisito 5: Detecção de Mudança de Status da Assinatura (Polling)

**User Story:** Como usuário, eu quero que o app detecte automaticamente que minha assinatura foi ativada após o pagamento, para que eu tenha acesso imediato ao conteúdo premium sem precisar reiniciar o app.

#### Critérios de Aceitação

1. WHEN a conclusão do checkout é detectada (via JavascriptChannel ou interceptação de URL), THE App_Flutter SHALL iniciar Polling_Status consultando GET /api/subscription/status a cada 3 segundos por no máximo 60 segundos (máximo de 20 tentativas)
2. WHEN o Polling_Status detecta que o campo `subscription_status` mudou para "active", THE App_Flutter SHALL encerrar o polling, fechar a WebView_Checkout, invalidar o cache conforme Requisito 6 e navegar o usuário para a Tela_Planos com mensagem de confirmação de assinatura ativada
3. IF o Polling_Status não detecta mudança de status para "active" dentro de 60 segundos, THEN THE App_Flutter SHALL encerrar o polling, fechar a WebView_Checkout e exibir na Tela_Planos uma mensagem informando que o pagamento está sendo processado e que a assinatura será ativada em breve, com botão "Verificar novamente" que dispara uma única consulta GET /api/subscription/status e atualiza a interface conforme o resultado
4. WHILE o Polling_Status está ativo, THE App_Flutter SHALL exibir uma tela intermediária de carregamento com mensagem "Confirmando seu pagamento..." e indicador de progresso visual animado
5. IF qualquer chamada individual do Polling_Status falha por erro de rede ou resposta HTTP 5xx, THEN THE App_Flutter SHALL ignorar a falha pontual e continuar o polling até o timeout de 60 segundos, sem interromper o fluxo
6. WHEN o Polling_Status detecta que o campo `subscription_status` mudou para "cancelled" ou "inactive", THEN THE App_Flutter SHALL encerrar o polling, fechar a WebView_Checkout e exibir mensagem informando que houve um problema com o pagamento e orientar o usuário a tentar novamente
7. IF o usuário fecha a WebView_Checkout ou navega para fora enquanto o Polling_Status está ativo, THEN THE App_Flutter SHALL encerrar o polling imediatamente e cancelar todas as requisições pendentes

### Requisito 6: Invalidação de Cache Após Mudança de Assinatura

**User Story:** Como usuário, eu quero que após minha assinatura ser ativada ou cancelada, todas as telas reflitam imediatamente meu novo status, para que eu veja os simulados premium desbloqueados sem precisar reiniciar o app.

#### Critérios de Aceitação

1. WHEN o status da assinatura do usuário muda (detectado por Polling_Status ou pelo botão "Restaurar compra" do Requisito 11), THE CacheManager SHALL invalidar as seguintes entradas de cache em uma única operação atômica sem estado parcialmente invalidado: GET /api/me, GET /api/exams e GET /api/plans
2. WHEN o cache é invalidado por mudança de assinatura e o usuário está visualizando a Tela_Planos, a Tela de listagem de simulados ou a Tela de detalhes do simulado, THE App_Flutter SHALL recarregar automaticamente os dados da tela visível a partir da API_Laravel sem exigir ação do usuário
3. WHEN o cache de GET /api/me é invalidado por mudança de assinatura e o usuário navega para uma tela que depende do status de assinatura (Tela_Planos, Tela de listagem de simulados, Tela de detalhes do simulado ou Dashboard), THE App_Flutter SHALL buscar dados atualizados da API_Laravel antes de renderizar a tela
4. WHEN o cache de GET /api/exams é invalidado, THE App_Flutter SHALL exibir os simulados com indicadores atualizados de premium/gratuito na próxima visualização da listagem, refletindo o novo status de acesso do usuário conforme retornado pela API_Laravel
5. WHEN o cache de GET /api/plans é invalidado, THE Tela_Planos SHALL exibir o status atualizado do plano ativo e as opções disponíveis conforme retornado pela API_Laravel na próxima visualização
6. IF o recarregamento de dados da API_Laravel falha por erro de rede após invalidação de cache, THEN THE App_Flutter SHALL exibir os últimos dados disponíveis com indicador informando que a atualização falhou e oferecer opção de tentar novamente

### Requisito 7: Ocultação da Bottom Navigation Durante Checkout

**User Story:** Como usuário, eu quero que durante o fluxo de checkout a navegação inferior seja ocultada, para que eu tenha uma experiência de pagamento imersiva e sem distrações, similar ao que acontece durante a realização de simulados.

#### Critérios de Aceitação

1. WHILE a rota `/checkout` está ativa no Router_App (incluindo WebView_Checkout e a tela intermediária de confirmação de pagamento), THE App_Flutter SHALL ocultar a Bottom_Navigation durante toda a permanência do usuário nessa rota, independentemente do estado de carregamento da página
2. WHEN a WebView_Checkout é fechada (por conclusão de pagamento, cancelamento pelo usuário, erro de carregamento ou abandono via diálogo de confirmação), THE App_Flutter SHALL exibir novamente a Bottom_Navigation com a aba que estava selecionada imediatamente antes da navegação para a rota `/checkout`
3. WHILE a tela intermediária de confirmação de pagamento (polling de status conforme Requisito 5) está visível, THE App_Flutter SHALL manter a Bottom_Navigation oculta até que o polling seja concluído e o usuário seja navegado de volta à Tela_Planos
4. IF o usuário retorna à Tela_Planos após o fluxo de checkout (por qualquer motivo: sucesso, cancelamento, timeout do polling ou erro), THEN THE App_Flutter SHALL garantir que a Bottom_Navigation esteja visível e funcional com todas as 4 abas acessíveis

### Requisito 8: Tratamento do Botão Voltar e Abandono do Checkout

**User Story:** Como usuário, eu quero que o botão voltar durante o checkout me pergunte se desejo abandonar o pagamento, para que eu não perca meu progresso acidentalmente.

#### Critérios de Aceitação

1. IF a WebView_Checkout possui histórico de navegação interno (páginas anteriores dentro do checkout), THEN THE App_Flutter SHALL navegar para a página anterior dentro da WebView quando o usuário pressiona o botão voltar do sistema
2. IF a WebView_Checkout não possui histórico de navegação interno (o usuário está na primeira página do checkout), THEN THE App_Flutter SHALL interceptar o gesto de voltar do sistema (botão físico ou gesture navigation do Android) e exibir um diálogo de confirmação com título "Abandonar checkout?" e texto "Você perderá o progresso do pagamento atual"
3. WHEN o usuário confirma o abandono no diálogo de confirmação, THE App_Flutter SHALL fechar a WebView_Checkout e retornar o usuário à Tela_Planos sem mensagem de cancelamento
4. WHEN o usuário toca em "Continuar" (cancela o abandono) no diálogo de confirmação, THE App_Flutter SHALL descartar o diálogo e manter a WebView_Checkout ativa com o fluxo de checkout intacto
5. WHILE o diálogo de confirmação está visível, THE WebView_Checkout SHALL permanecer ativa em background sem interromper processos em andamento (ex: processamento de pagamento em curso)

### Requisito 9: Rota de Navegação e Integração com GoRouter

**User Story:** Como desenvolvedor, eu quero que o fluxo de checkout tenha uma rota dedicada no GoRouter, para que a navegação seja consistente com o restante do app e suporte deep linking futuro.

#### Critérios de Aceitação

1. THE Router_App SHALL registrar a rota `/checkout` no GoRouter como rota fora do ShellRoute (sem Bottom_Navigation), aceitando o parâmetro `checkoutUrl` como query parameter na URI (e.g. `/checkout?checkoutUrl=<encoded_url>`) para que o valor sobreviva a redirecionamentos e deep linking
2. WHEN o Router_App navega para `/checkout` com um `checkoutUrl` válido (string não vazia com esquema `https`), THE App_Flutter SHALL exibir a tela da WebView_Checkout carregando a URL recebida no parâmetro `checkoutUrl`
3. IF a rota `/checkout` é acessada sem o parâmetro `checkoutUrl` ou com valor vazio ou com esquema diferente de `https`, THEN THE Router_App SHALL redirecionar o usuário para a Tela_Planos (`/planos`)
4. IF um usuário não autenticado tenta acessar a rota `/checkout`, THEN THE Router_App SHALL redirecionar para a tela de login e armazenar a URI completa (incluindo query parameter `checkoutUrl`) como deep link pendente, de modo que após autenticação bem-sucedida o usuário seja direcionado de volta para `/checkout` com o mesmo `checkoutUrl`
5. WHEN o usuário autenticado completa o login com um deep link pendente para `/checkout`, THE Router_App SHALL navegar automaticamente para `/checkout` com o `checkoutUrl` original preservado no query parameter

### Requisito 10: Cancelamento de Assinatura Ativa

**User Story:** Como usuário assinante, eu quero poder cancelar minha assinatura diretamente pelo app, para que eu tenha controle sobre meu plano sem precisar acessar o site.

#### Critérios de Aceitação

1. WHEN o usuário possui `subscription_status` igual a "active", THE Tela_Planos SHALL exibir um botão "Cancelar assinatura" visível na seção do plano ativo
2. WHEN o usuário toca em "Cancelar assinatura", THE App_Flutter SHALL exibir um diálogo modal de confirmação descrevendo que o acesso premium será mantido até a data de expiração (`subscription_expires_at` formatada no padrão dd/MM/yyyy) e solicitando confirmação explícita com botões "Confirmar cancelamento" e "Manter assinatura"
3. WHEN o usuário confirma o cancelamento no diálogo, THE App_Flutter SHALL chamar POST /api/subscription/cancel com timeout de 15 segundos e exibir indicador de carregamento no botão "Confirmar cancelamento" até receber resposta, desabilitando ambos os botões do diálogo durante o processamento
4. WHEN a API_Laravel retorna sucesso (HTTP 200) para POST /api/subscription/cancel, THE App_Flutter SHALL atualizar o status exibido na Tela_Planos para "cancelled", invalidar o cache conforme Requisito 6 e exibir mensagem confirmando que a assinatura foi cancelada informando a data até a qual o acesso premium permanece ativo
5. IF a chamada POST /api/subscription/cancel falha por erro de rede, timeout de 15 segundos ou retorna erro do servidor (HTTP 5xx), THEN THE App_Flutter SHALL fechar o diálogo, exibir mensagem de erro e manter o estado atual da assinatura inalterado na UI
6. WHEN o usuário toca em "Manter assinatura" no diálogo de confirmação, THE App_Flutter SHALL fechar o diálogo e manter o estado atual sem executar nenhuma ação

### Requisito 11: Restauração e Sincronização Manual de Assinatura

**User Story:** Como usuário, eu quero poder sincronizar manualmente o status da minha assinatura, para que caso o webhook demore a processar eu possa forçar a verificação e ter acesso ao conteúdo pago.

#### Critérios de Aceitação

1. THE Tela_Planos SHALL incluir botão "Restaurar compra" visível independente do status atual da assinatura
2. WHEN o usuário toca em "Restaurar compra", THE App_Flutter SHALL chamar GET /api/subscription/status e comparar o resultado com o status local atual
3. WHEN GET /api/subscription/status retorna um status diferente do armazenado localmente, THE App_Flutter SHALL atualizar o status local, invalidar o cache conforme Requisito 6 e exibir mensagem confirmando a sincronização
4. WHEN GET /api/subscription/status retorna o mesmo status já armazenado localmente, THE App_Flutter SHALL exibir mensagem informando que a assinatura já está sincronizada
5. IF a chamada GET /api/subscription/status falha por erro de rede ou timeout de 15 segundos, THEN THE App_Flutter SHALL exibir mensagem de erro com opção de tentar novamente
6. IF a chamada GET /api/subscription/status retorna HTTP 401, THEN THE Gerenciador_De_Sessão SHALL encerrar a sessão e redirecionar o usuário para a tela de login conforme o fluxo de sessão expirada
7. WHILE a chamada GET /api/subscription/status está em andamento, THE App_Flutter SHALL exibir indicador de carregamento no botão "Restaurar compra" e desabilitar interações duplicadas
