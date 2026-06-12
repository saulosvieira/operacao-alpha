# Requirements Document

## Introduction

Este documento define os requisitos para o aplicativo híbrido Flutter "Operação Alfa" que combina telas nativas Flutter (Material Design 3) com WebView para funcionalidades complexas. O objetivo é substituir o aplicativo Android atual (WebView pura) por uma solução que satisfaça os requisitos do Google Play para funcionalidade nativa real, enquanto mantém a interface complexa de realização de simulados via WebView. O aplicativo será multiplataforma (Android e iOS) a partir de um único codebase Flutter.

## Glossary

- **App_Flutter**: O aplicativo Flutter híbrido multiplataforma que combina telas nativas com WebView
- **Tela_Nativa**: Qualquer tela construída com widgets Flutter e Material Design 3, sem uso de WebView
- **WebView_Simulado**: O componente WebView embutido no Flutter que carrega a interface de realização de simulados do frontend React (PWA) servido pela API_Laravel no mesmo domínio
- **API_Laravel**: O backend Laravel existente que fornece endpoints REST sob o prefixo `/api`, autenticados via Laravel Sanctum em modo API token (Bearer), com base URL `https://operacaoalfa.com.br` em produção e `https://operacao-alfa.homolog.mydev.com.br` em homologação
- **Sanctum_Bearer_Token**: Token opaco emitido por `POST /api/login` ou `POST /api/register` no campo `token` da resposta JSON, usado em todas as requisições autenticadas no header `Authorization: Bearer <token>`
- **Gerenciador_De_Autenticação**: O componente Flutter responsável por autenticar o usuário via API_Laravel, persistir o Sanctum_Bearer_Token e injetá-lo nas chamadas HTTP nativas
- **FCM_Service**: O serviço Firebase Cloud Messaging responsável por receber e exibir notificações push no dispositivo
- **Gerenciador_De_Sessão**: O componente que persiste o Sanctum_Bearer_Token em armazenamento seguro e o sincroniza com a WebView_Simulado via injeção de localStorage antes do carregamento da URL
- **Gerenciador_De_Conectividade**: O componente que monitora o estado da conexão de rede e exibe feedback ao usuário
- **Router_App**: O sistema de navegação do Flutter que gerencia rotas entre telas nativas e a WebView
- **Deep_Link_Handler**: O componente que processa deep links e URLs de notificações para navegar à tela correta
- **URL_Tentativa_Pattern**: Expressão regular que identifica uma URL do React PWA correspondente a uma tentativa de simulado em andamento: `^/simulado/[^/]+/(tentativa|executar)/[^/]+/?$`
- **URL_Resultado_Pattern**: Expressão regular que identifica uma URL do React PWA correspondente ao resultado de uma tentativa: `^/simulado/[^/]+/resultado/[^/]+/?$`
- **Domínio_Sistema**: O conjunto de hosts oficiais do sistema, contendo `operacaoalfa.com.br` (produção) e `operacao-alfa.homolog.mydev.com.br` (homologação)

## Requirements

### Requisito 1: Tela de Login Nativa

**User Story:** Como usuário, eu quero fazer login no aplicativo através de uma tela nativa Flutter, para que a experiência de autenticação seja rápida e com aparência nativa do dispositivo.

#### Critérios de Aceitação

1. THE App_Flutter SHALL exibir uma Tela_Nativa de login com campos de e-mail (máximo 254 caracteres) e senha (entre 6 e 128 caracteres) seguindo Material Design 3
2. WHEN o usuário submete credenciais válidas, THE Gerenciador_De_Autenticação SHALL autenticar via endpoint POST /api/login da API_Laravel e armazenar o Sanctum_Bearer_Token retornado no campo `token` da resposta JSON utilizando o armazenamento seguro da plataforma (Keychain no iOS, Keystore no Android via flutter_secure_storage)
3. WHEN a autenticação é bem-sucedida, THE Router_App SHALL navegar o usuário para a tela de dashboard
4. WHEN a API_Laravel retorna HTTP 401 com mensagem "Credenciais inválidas", THE Tela_Nativa de login SHALL exibir a mensagem retornada pela API_Laravel sem recarregar a tela
5. IF a API_Laravel não responde dentro de 15 segundos durante tentativa de login, THEN THE App_Flutter SHALL exibir mensagem informando que o servidor está temporariamente indisponível
6. THE Tela_Nativa de login SHALL incluir opção de "Lembrar-me" que, quando marcada, persiste o Sanctum_Bearer_Token em armazenamento seguro mantendo a sessão ativa entre reinicializações do aplicativo até que o token seja revogado pelo backend; quando desmarcada, o token é mantido apenas em memória e descartado ao fechar o aplicativo
7. THE Tela_Nativa de login SHALL incluir link para registro de nova conta que navega para a tela de cadastro nativa
8. WHEN o usuário tenta submeter o formulário com campo de e-mail vazio, formato de e-mail inválido, ou campo de senha vazio, THEN THE Tela_Nativa de login SHALL exibir mensagem de validação inline no campo correspondente e impedir o envio à API_Laravel
9. WHILE o Gerenciador_De_Autenticação está processando a requisição de login, THE Tela_Nativa de login SHALL desabilitar o botão de login e exibir um indicador de carregamento até que a resposta seja recebida ou o timeout de 15 segundos seja atingido
10. WHEN a API_Laravel retorna HTTP 429 (rate limit do throttle:login excedido), THE Tela_Nativa de login SHALL exibir mensagem informando o usuário a aguardar antes de tentar novamente

### Requisito 2: Tela de Cadastro Nativa

**User Story:** Como novo usuário, eu quero me cadastrar no aplicativo através de uma tela nativa, para que eu possa criar minha conta de forma rápida e intuitiva.

#### Critérios de Aceitação

1. THE App_Flutter SHALL exibir uma Tela_Nativa de cadastro com campos de nome (máximo 255 caracteres), e-mail, senha (mínimo 8 caracteres) e confirmação de senha (campo `password_confirmation`) seguindo Material Design 3
2. WHEN o usuário submete dados válidos de cadastro, THE Gerenciador_De_Autenticação SHALL registrar o usuário via endpoint POST /api/register da API_Laravel e armazenar o Sanctum_Bearer_Token retornado no campo `token` da resposta JSON
3. WHEN o cadastro é bem-sucedido (HTTP 201), THE Gerenciador_De_Autenticação SHALL autenticar automaticamente o usuário com o token recebido e o Router_App SHALL navegar para a tela de dashboard
4. WHEN a API_Laravel retorna HTTP 422 com erros de validação (e-mail duplicado, senha curta, confirmação não confere), THE Tela_Nativa de cadastro SHALL exibir cada mensagem de erro retornada pela API junto ao campo correspondente conforme estrutura `errors.{campo}` do Laravel
5. THE Tela_Nativa de cadastro SHALL validar localmente antes de enviar à API_Laravel: formato de e-mail válido (contém @ e domínio), senha com mínimo de 8 caracteres, e confirmação de senha idêntica ao campo senha
6. IF a chamada ao endpoint POST /api/register falhar por erro de rede ou indisponibilidade da API_Laravel, THEN THE Tela_Nativa de cadastro SHALL exibir uma mensagem de erro indicando falha de conexão e manter os dados preenchidos pelo usuário nos campos do formulário (exceto os campos de senha por segurança)
7. WHEN o cadastro é bem-sucedido, THE FCM_Service SHALL ser acionado conforme o Requisito 8 para solicitar permissão de notificação ao usuário

### Requisito 3: Dashboard Nativa com Estatísticas

**User Story:** Como usuário, eu quero visualizar meu progresso e estatísticas em uma tela nativa, para que eu tenha acesso rápido às informações mais importantes sobre meu desempenho.

#### Critérios de Aceitação

1. THE App_Flutter SHALL exibir uma Tela_Nativa de dashboard com estatísticas do usuário obtidas via endpoint GET /api/performance/statistics da API_Laravel e posição no ranking obtida via endpoint GET /api/ranking/my-position
2. THE Tela_Nativa de dashboard SHALL exibir o número total de simulados realizados (campo total_exams_completed), taxa de acerto percentual (campo accuracy_percentage) e posição numérica no ranking (campo position)
3. WHEN o usuário realiza pull-to-refresh na tela de dashboard, THE App_Flutter SHALL recarregar as estatísticas da API_Laravel e atualizar todos os campos exibidos em no máximo 10 segundos
4. WHILE os dados estão sendo carregados da API_Laravel, THE Tela_Nativa de dashboard SHALL exibir indicadores de carregamento (shimmer/skeleton) nos campos de dados
5. IF a API_Laravel retorna erro ao carregar estatísticas, THEN THE Tela_Nativa de dashboard SHALL exibir os últimos dados em cache com um indicador textual contendo a data e hora da última atualização bem-sucedida
6. THE Tela_Nativa de dashboard SHALL exibir atalhos de navegação para a listagem de simulados disponíveis e para o histórico de tentativas do usuário
7. IF o endpoint GET /api/ranking/my-position retorna 404 (usuário sem posição no ranking), THEN THE Tela_Nativa de dashboard SHALL exibir uma mensagem indicando que o usuário ainda não possui posição no ranking
8. IF o usuário não possui simulados realizados (total_exams_completed igual a zero), THEN THE Tela_Nativa de dashboard SHALL exibir um estado vazio com mensagem orientando o usuário a realizar seu primeiro simulado

### Requisito 4: Listagem de Simulados Nativa

**User Story:** Como usuário, eu quero navegar pela lista de simulados disponíveis em uma tela nativa, para que eu possa encontrar e iniciar simulados de forma rápida e fluida.

#### Critérios de Aceitação

1. THE App_Flutter SHALL exibir uma Tela_Nativa de listagem de simulados obtidos via endpoint GET /api/exams da API_Laravel, que atualmente retorna a coleção completa filtrável por query string `career_id`
2. THE Tela_Nativa de listagem SHALL exibir para cada simulado: título (`title`), número de questões (`numQuestions`), tempo de duração em minutos (`durationMin`), indicador de simulado gratuito vs premium (campo `isFree`) e indicador de tentativa anterior quando disponível
3. THE Tela_Nativa de listagem SHALL suportar filtro por carreira utilizando dados do endpoint público GET /api/careers da API_Laravel, repassando o ID da carreira selecionada como parâmetro `career_id` para GET /api/exams; o filtro SHALL operar em modo seleção única e SHALL exibir uma opção "Todas as carreiras" que limpa o filtro
4. THE Tela_Nativa de listagem SHALL renderizar a lista completa retornada pela API_Laravel utilizando rolagem virtualizada (ListView.builder) para suportar até 1000 itens sem degradação perceptível de performance
5. WHEN o usuário toca em um simulado na listagem, THE Router_App SHALL navegar para a tela de detalhes do simulado
6. WHILE os dados estão sendo carregados, THE Tela_Nativa de listagem SHALL exibir indicadores de carregamento (shimmer/skeleton)
7. WHEN o usuário realiza pull-to-refresh, THE App_Flutter SHALL recarregar a lista de simulados da API_Laravel substituindo os dados exibidos anteriormente
8. IF a chamada ao endpoint GET /api/exams falhar por erro de rede ou resposta de erro do servidor, THEN THE Tela_Nativa de listagem SHALL exibir uma mensagem de erro com um botão para tentar novamente
9. IF a lista de simulados retornada estiver vazia (nenhum simulado disponível ou nenhum resultado para o filtro aplicado), THEN THE Tela_Nativa de listagem SHALL exibir uma mensagem indicando que não há simulados disponíveis

### Requisito 5: Tela de Detalhes do Simulado Nativa

**User Story:** Como usuário, eu quero ver os detalhes de um simulado antes de iniciá-lo, para que eu possa decidir se quero realizá-lo agora.

#### Critérios de Aceitação

1. THE App_Flutter SHALL exibir uma Tela_Nativa de detalhes do simulado com informações obtidas via endpoint GET /api/exams/{id} da API_Laravel
2. THE Tela_Nativa de detalhes SHALL exibir: título (`title`), descrição (`description`), número de questões (`numQuestions`), tempo de duração em minutos (`durationMin`) e indicador de gratuidade (`isFree`); a lista de tentativas anteriores SHALL ser exibida quando o endpoint retornar o atributo correspondente
3. THE Tela_Nativa de detalhes SHALL incluir um botão "Iniciar Simulado" que inicia o fluxo de realização do simulado
4. WHEN o usuário toca em "Iniciar Simulado", THE App_Flutter SHALL chamar o endpoint POST /api/exams/{id}/start da API_Laravel e, com o ID da tentativa retornado, navegar para a WebView_Simulado carregando a URL `{Domínio_Sistema}/simulado/{examId}/tentativa/{attemptId}`
5. IF o usuário não possui assinatura ativa (campo `subscription_status` diferente de `active` ou `subscription_expires_at` no passado, conforme retornado por GET /api/me) e o simulado não é gratuito (`isFree=false`), THEN THE Tela_Nativa de detalhes SHALL desabilitar o botão "Iniciar Simulado", exibir indicador visual de conteúdo premium e exibir botão que navega para a tela de planos de assinatura definida no Requisito 19
6. IF a chamada ao endpoint GET /api/exams/{id} retorna HTTP 404, THEN THE App_Flutter SHALL exibir mensagem indicando que o simulado não foi encontrado e oferecer opção de voltar à listagem de simulados
7. IF a chamada ao endpoint POST /api/exams/{id}/start falha, THEN THE App_Flutter SHALL exibir mensagem de erro indicando que não foi possível iniciar o simulado e manter o usuário na Tela_Nativa de detalhes
8. WHILE os dados do simulado estão sendo carregados da API_Laravel, THE Tela_Nativa de detalhes SHALL exibir indicadores de carregamento (shimmer/skeleton)

### Requisito 6: Realização de Simulado via WebView

**User Story:** Como usuário, eu quero realizar simulados na interface web existente dentro do aplicativo, para que eu tenha acesso à experiência completa de realização de provas com cronômetro, navegação entre questões e submissão de respostas.

#### Critérios de Aceitação

1. WHEN o usuário inicia um simulado, THE App_Flutter SHALL abrir a WebView_Simulado carregando a URL da tentativa ativa no React PWA do Domínio_Sistema (path no formato `/simulado/{examId}/tentativa/{attemptId}`) dentro de no máximo 30 segundos
2. THE WebView_Simulado SHALL receber o Sanctum_Bearer_Token via injeção de `localStorage` antes do `loadUrl`, replicando o mecanismo de autenticação que o React PWA já utiliza, evitando que o usuário precise fazer login novamente
3. THE WebView_Simulado SHALL habilitar JavaScript e armazenamento DOM (localStorage/sessionStorage) para funcionamento completo do React PWA
4. THE WebView_Simulado SHALL expor ao JavaScript da página um canal nativo (ex.: `JavascriptChannel("OperacaoAlfaApp")`) que aceita mensagens estruturadas em JSON com pelo menos os tipos `examFinished` (com `examId` e `attemptId`) e `requestExit`
5. WHILE o usuário está realizando o simulado na WebView_Simulado, THE App_Flutter SHALL interceptar o gesto de voltar do sistema e exibir um diálogo de confirmação perguntando se o usuário deseja abandonar o simulado, em vez de navegar para trás imediatamente
6. THE App_Flutter SHALL detectar a finalização do simulado por dois mecanismos complementares: (a) recebimento de mensagem `examFinished` no canal nativo, ou (b) quando ainda não houver instrumentação no React PWA, observação de que a URL atual da WebView_Simulado corresponde ao URL_Resultado_Pattern
7. WHEN a finalização do simulado é detectada por qualquer um dos mecanismos do critério 6, THE Router_App SHALL navegar para a tela nativa de listagem de simulados e invalidar as entradas de cache de estatísticas, ranking e histórico conforme o Requisito 18
8. IF ocorre erro de rede durante a realização do simulado, THEN THE WebView_Simulado SHALL exibir mensagem de erro com opção de tentar novamente, e ao reconectar, a WebView SHALL recarregar a última URL do simulado preservando a tentativa ativa no backend (respostas já submetidas permanecem salvas)
9. IF a WebView_Simulado falha ao carregar a URL da tentativa dentro de 30 segundos ou recebe erro de rede no carregamento inicial, THEN THE App_Flutter SHALL exibir mensagem de erro com opção de tentar novamente ou voltar para a tela de detalhes do simulado
10. IF o aplicativo é minimizado (background) por mais de 5 minutos durante uma tentativa em andamento e o sistema operacional encerra o processo, THEN ao reabrir, THE App_Flutter SHALL detectar a tentativa pendente armazenada localmente e oferecer ao usuário a opção de retomar a tentativa carregando a URL_Tentativa_Pattern correspondente
11. THE WebView_Simulado SHALL navegar exclusivamente dentro do Domínio_Sistema; URLs externas SHALL ser abertas no navegador padrão do dispositivo via `url_launcher`

### Requisito 7: Sincronização de Sessão entre Nativo e WebView

**User Story:** Como usuário, eu quero que minha sessão de login funcione tanto nas telas nativas quanto na WebView, para que eu não precise fazer login múltiplas vezes.

#### Critérios de Aceitação

1. WHEN o usuário faz login na Tela_Nativa, THE Gerenciador_De_Sessão SHALL persistir o Sanctum_Bearer_Token em armazenamento seguro (flutter_secure_storage) usando uma chave dedicada
2. WHEN a WebView_Simulado é aberta, THE Gerenciador_De_Sessão SHALL injetar o Sanctum_Bearer_Token no `localStorage` da WebView na chave utilizada pelo React PWA antes de iniciar o `loadUrl`, executando JavaScript do tipo `window.localStorage.setItem('<chave>', '<token>')`
3. WHEN o usuário faz logout em qualquer contexto (nativo ou WebView), THE Gerenciador_De_Sessão SHALL chamar POST /api/logout para revogar o token no servidor, remover o token do armazenamento seguro, limpar todos os cookies do CookieManager da WebView, e limpar `localStorage` e `sessionStorage` da WebView em até 2 segundos
4. IF a chamada POST /api/logout falhar por erro de rede, THEN THE Gerenciador_De_Sessão SHALL prosseguir com a limpeza local do token e dos dados da WebView, registrando o erro para nova tentativa de revogação na próxima inicialização com conexão
5. THE Gerenciador_De_Sessão SHALL adicionar o header `Authorization: Bearer {token}` em todas as requisições HTTP nativas para a API_Laravel sob endpoints autenticados
6. IF qualquer endpoint autenticado da API_Laravel retorna HTTP 401 (token inválido ou revogado), THEN THE Gerenciador_De_Sessão SHALL limpar o token armazenado, encerrar a sessão local e redirecionar o usuário para a tela de login nativa em até 3 segundos
7. THE Gerenciador_De_Sessão SHALL persistir o Sanctum_Bearer_Token entre reinicializações do aplicativo apenas quando a opção "Lembrar-me" estiver marcada (Requisito 1.6); caso contrário, o token vive apenas em memória durante a execução do aplicativo

### Requisito 8: Notificações Push via Firebase Cloud Messaging

**User Story:** Como usuário, eu quero receber notificações push sobre novos simulados, resultados e atualizações, para que eu esteja sempre informado sobre novidades da plataforma.

#### Critérios de Aceitação

1. WHEN o usuário visualiza pela primeira vez a tela de dashboard após estar autenticado (seja por login, registro ou retomada de sessão persistida), THE FCM_Service SHALL solicitar permissão de notificação ao usuário caso o estado de permissão seja `notDetermined`
2. WHEN o usuário concede permissão, THE FCM_Service SHALL obter o token de registro FCM do dispositivo
3. IF o usuário negar a permissão de notificação, THEN THE App_Flutter SHALL continuar o fluxo normal do aplicativo sem notificações push e não solicitar permissão novamente até que o usuário desinstale e reinstale o aplicativo ou conceda permissão manualmente nas configurações do sistema
4. WHEN um token FCM é obtido, THE App_Flutter SHALL enviar o token ao backend via endpoint POST /api/notifications/fcm/subscribe com body `{ "token": "<fcm_token>", "device_id": "<uuid_persistido>" }`, onde `device_id` é um UUID gerado na primeira instalação e persistido em flutter_secure_storage
5. WHILE o aplicativo está em foreground, WHEN uma mensagem FCM data-only é recebida, THE FCM_Service SHALL exibir uma notificação local do sistema (canal de notificação dedicado) contendo `title`, `body` e armazenando `url` como payload do tap, conforme estrutura `data: { title, body, url }` enviada pelo backend
6. WHILE o aplicativo está em background ou encerrado, WHEN uma mensagem FCM data-only é recebida, THE FCM_Service SHALL exibir uma notificação do sistema operacional contendo o título e o corpo extraídos do payload da mensagem
7. IF uma notificação push é recebida com payload sem `title` E sem `body`, THEN THE FCM_Service SHALL descartar a notificação sem exibi-la ao usuário
8. WHEN o usuário toca em uma notificação, THE Deep_Link_Handler SHALL abrir o aplicativo e navegar para a tela correspondente à URL contida no campo `url` do payload `data` da notificação, conforme regras do Requisito 11
9. IF a URL contida no campo `url` do payload `data` estiver ausente ou for inválida, THEN THE Deep_Link_Handler SHALL abrir o aplicativo na tela inicial (dashboard)
10. WHEN o token FCM é atualizado pelo Firebase (callback `onTokenRefresh`), THE FCM_Service SHALL enviar o novo token ao backend via POST /api/notifications/fcm/subscribe e solicitar a remoção do token anterior via POST /api/notifications/fcm/unsubscribe com body `{ "device_id": "<uuid>" }`
11. IF o envio do token FCM ao backend falhar, THEN THE App_Flutter SHALL armazenar o token localmente como pendente e tentar reenviar na próxima inicialização do aplicativo, por no máximo 5 tentativas consecutivas com falha
12. WHEN o usuário faz logout, THE FCM_Service SHALL chamar POST /api/notifications/fcm/unsubscribe com o `device_id` do dispositivo antes de descartar o token armazenado

### Requisito 9: Tela de Perfil Nativa

**User Story:** Como usuário, eu quero visualizar e editar meu perfil em uma tela nativa, para que eu possa gerenciar minhas informações pessoais de forma rápida.

#### Critérios de Aceitação

1. WHEN a Tela_Nativa de perfil é aberta, THE App_Flutter SHALL obter os dados do usuário via endpoint GET /api/user/profile da API_Laravel e exibir um indicador de carregamento até que a resposta seja recebida ou até um timeout de 15 segundos
2. THE Tela_Nativa de perfil SHALL exibir os seguintes campos: nome, e-mail, telefone, status da assinatura (`subscription_status`) e data de expiração da assinatura (`subscription_expires_at`) quando disponível
3. THE Tela_Nativa de perfil SHALL permitir edição dos campos `name`, `email` e `phone` (que são os campos atualmente aceitos pelo `UpdateProfileRequest` do backend conforme `app/Http/Requests/User/UpdateProfileRequest.php`); a edição de senha SHALL ser oferecida em fluxo separado dentro desta tela com campo `password_confirmation` obrigatório
4. WHEN o usuário salva alterações, THE App_Flutter SHALL enviar PUT /api/user/profile com apenas os campos modificados (`sometimes` no backend), não o objeto inteiro
5. WHEN a atualização de perfil é bem-sucedida (HTTP 200), THE Tela_Nativa de perfil SHALL exibir uma mensagem de confirmação visível por no mínimo 3 segundos e invalidar o cache de perfil conforme Requisito 18
6. IF a requisição GET /api/user/profile falha por erro de rede ou retorna um código de erro do servidor, THEN THE Tela_Nativa de perfil SHALL exibir uma mensagem de erro indicando a indisponibilidade e um botão para tentar novamente
7. IF a requisição PUT /api/user/profile retorna HTTP 422 com erros de validação, THEN THE Tela_Nativa de perfil SHALL exibir cada mensagem de erro junto ao campo correspondente conforme estrutura `errors.{campo}` e manter os dados editados pelo usuário no formulário
8. THE Tela_Nativa de perfil SHALL incluir opção de logout que aciona o Gerenciador_De_Sessão conforme Requisito 7.3 e redireciona o usuário para a tela de login após a limpeza
9. THE Tela_Nativa de perfil SHALL incluir opção de "Excluir conta" que aciona o fluxo de exclusão de conta definido no Requisito 20
10. THE Tela_Nativa de perfil SHALL exibir um placeholder padrão no lugar da foto de perfil; a edição de foto NÃO SHALL ser oferecida nesta versão até que o backend implemente o endpoint correspondente conforme nota técnica neste documento

### Requisito 10: Navegação Principal com Bottom Navigation

**User Story:** Como usuário, eu quero navegar entre as seções principais do aplicativo usando uma barra de navegação inferior, para que a navegação seja intuitiva e acessível.

#### Critérios de Aceitação

1. THE App_Flutter SHALL exibir uma barra de navegação inferior (BottomNavigationBar) com exatamente 4 seções na ordem: Dashboard, Simulados, Ranking e Perfil, cada uma com ícone e label de texto visíveis simultaneamente
2. WHEN o App_Flutter é iniciado, THE App_Flutter SHALL exibir a seção Dashboard como aba ativa por padrão
3. WHEN o usuário toca em um item da navegação inferior, THE Router_App SHALL navegar para a Tela_Nativa correspondente em no máximo 300ms, mantendo as demais seções em memória sem reinicializar seu conteúdo
4. THE App_Flutter SHALL destacar visualmente o item ativo na barra de navegação inferior diferenciando-o dos itens inativos por cor de ícone e label
5. THE App_Flutter SHALL manter o estado de cada seção ao alternar entre abas, incluindo posição de scroll e dados já carregados da API, de modo que ao retornar a uma seção previamente visitada o usuário veja o mesmo conteúdo sem novo carregamento
6. WHILE a WebView_Simulado está exibindo uma URL que corresponde ao URL_Tentativa_Pattern, THE App_Flutter SHALL ocultar a barra de navegação inferior
7. WHEN a WebView_Simulado navega para fora de uma URL que corresponde ao URL_Tentativa_Pattern, THE App_Flutter SHALL exibir novamente a barra de navegação inferior com a aba previamente ativa selecionada
8. THE AppBar exibida em todas as quatro Telas_Nativas principais SHALL conter um ícone de sino com badge numérico que abre a Tela_Nativa de notificações definida no Requisito 16, servindo como ponto de entrada para o histórico de notificações

### Requisito 11: Deep Linking e Navegação por URL

**User Story:** Como usuário, eu quero que links compartilhados e URLs de notificações abram diretamente na tela correta do aplicativo, para que eu acesse o conteúdo desejado sem navegação manual.

#### Critérios de Aceitação

1. THE Deep_Link_Handler SHALL processar deep links no formato de esquema customizado (`operacaoalfa://`) e App Links / Universal Links cujo host pertença ao Domínio_Sistema
2. WHEN um deep link contendo o path de um simulado específico (ex: `/simulado/:id`) é recebido, THE Router_App SHALL navegar diretamente para a Tela_Nativa de detalhes do simulado correspondente
3. WHEN um deep link cujo path corresponde ao URL_Tentativa_Pattern é recebido, THE Router_App SHALL abrir a WebView_Simulado com a tentativa ativa correspondente
4. WHEN um deep link cujo path corresponde ao URL_Resultado_Pattern é recebido, THE Router_App SHALL navegar para a tela de detalhes do simulado e exibir uma seção destacada com o resultado da tentativa identificada na URL
5. IF o usuário não está autenticado quando um deep link é recebido, THEN THE App_Flutter SHALL armazenar o deep link pendente em memória, exibir a tela de login e, após autenticação bem-sucedida, navegar automaticamente para a URL de destino original
6. THE App_Flutter SHALL configurar no AndroidManifest.xml intent filters para o esquema `operacaoalfa://` e para os hosts do Domínio_Sistema com `android:autoVerify="true"`, e o servidor SHALL publicar o arquivo `/.well-known/assetlinks.json` correspondente; no iOS, o app SHALL declarar Associated Domains para os mesmos hosts e o servidor SHALL publicar `/.well-known/apple-app-site-association`
7. IF um deep link é recebido com um path não reconhecido ou inválido, THEN THE App_Flutter SHALL navegar para a tela inicial (dashboard) em vez de exibir uma tela em branco ou erro
8. THE Deep_Link_Handler SHALL aceitar apenas links cujo host pertence ao Domínio_Sistema; links com hosts arbitrários SHALL ser ignorados ou abertos no navegador externo conforme o caso

### Requisito 12: Tratamento de Conectividade de Rede

**User Story:** Como usuário, eu quero ser informado quando não houver conexão com a internet, para que eu entenda por que o conteúdo não está carregando.

#### Critérios de Aceitação

1. WHEN o App_Flutter detecta ausência de conexão de rede, THE Gerenciador_De_Conectividade SHALL exibir um banner não-dismissível na parte superior da tela contendo uma mensagem indicando ausência de conexão, dentro de 3 segundos após a perda de conectividade
2. WHILE não há conexão de rede e existem dados em cache para a tela atual, THE App_Flutter SHALL exibir os dados previamente carregados nas telas nativas
3. IF não há conexão de rede e não existem dados em cache para a tela solicitada, THEN THE App_Flutter SHALL exibir uma tela de estado vazio com mensagem indicando indisponibilidade offline e um botão para tentar novamente
4. WHEN a conexão de rede é restaurada, THE Gerenciador_De_Conectividade SHALL remover o banner de ausência de conexão e recarregar o conteúdo da tela atualmente visível dentro de 5 segundos
5. IF a WebView_Simulado perde conexão durante realização de simulado, THEN THE App_Flutter SHALL exibir um alerta sobreposto à WebView informando a perda de conexão, sem fechar a WebView e sem descartar as respostas já registradas pelo usuário
6. WHEN a conexão é restaurada durante realização de simulado, THE WebView_Simulado SHALL permitir que o usuário continue o simulado preservando a questão atual, as respostas já selecionadas e o tempo decorrido

### Requisito 13: Tela de Ranking Nativa

**User Story:** Como usuário, eu quero visualizar o ranking de desempenho em uma tela nativa, para que eu possa acompanhar minha posição em relação a outros usuários.

#### Critérios de Aceitação

1. THE App_Flutter SHALL exibir uma Tela_Nativa de ranking com dados obtidos via endpoint GET /api/ranking da API_Laravel, exibindo por padrão o ranking do tipo "weekly" com limite de 100 entradas
2. THE Tela_Nativa de ranking SHALL exibir a lista de usuários com posição (numérica), nome e pontuação para cada entrada retornada pela API
3. THE Tela_Nativa de ranking SHALL destacar visualmente a entrada do usuário atual na lista utilizando uma cor de fundo diferenciada das demais entradas
4. WHEN o usuário realiza pull-to-refresh, THE App_Flutter SHALL recarregar os dados de ranking da API_Laravel e exibir um indicador de carregamento até que a resposta seja recebida ou até um timeout de 15 segundos
5. THE Tela_Nativa de ranking SHALL exibir a posição do usuário obtida via endpoint GET /api/ranking/my-position da API_Laravel em uma seção fixa visível sem necessidade de rolagem
6. IF a chamada ao endpoint GET /api/ranking falhar ou retornar erro, THEN THE App_Flutter SHALL exibir uma mensagem de erro indicando a indisponibilidade dos dados e um botão para tentar novamente
7. IF o endpoint GET /api/ranking/my-position retornar 404 (usuário sem posição no ranking), THEN THE App_Flutter SHALL exibir uma mensagem informando que o usuário precisa completar simulados para aparecer no ranking
8. WHILE os dados de ranking estão sendo carregados da API_Laravel, THE Tela_Nativa SHALL exibir um indicador de carregamento no lugar da lista

### Requisito 14: Suporte Multiplataforma (Android e iOS)

**User Story:** Como equipe de desenvolvimento, eu quero que o aplicativo funcione em Android e iOS a partir de um único codebase Flutter, para que possamos atingir ambas as plataformas com menor esforço de manutenção.

#### Critérios de Aceitação

1. THE App_Flutter SHALL compilar sem erros e executar em dispositivos Android (minSdk 26 / Android 8.0+, alinhado ao app atual em `android/app/build.gradle.kts`) e iOS (iOS 13+), exibindo a tela inicial do aplicativo com conteúdo funcional em ambas as plataformas
2. THE App_Flutter SHALL utilizar o sistema de temas Material Design 3 do Flutter (ThemeData com useMaterial3: true) para renderizar componentes visuais de forma consistente em ambas as plataformas
3. THE App_Flutter SHALL utilizar plugins Flutter multiplataforma para WebView (webview_flutter), notificações (firebase_messaging) e armazenamento seguro (flutter_secure_storage)
4. THE App_Flutter SHALL configurar no AndroidManifest.xml as permissões INTERNET, ACCESS_NETWORK_STATE e POST_NOTIFICATIONS, e no Info.plist as chaves NSAppTransportSecurity (sem exceções, requerendo HTTPS em todas as conexões para o Domínio_Sistema)
5. THE App_Flutter SHALL realizar todas as comunicações de rede com a API_Laravel exclusivamente via HTTPS; conexões HTTP em texto plano SHALL ser permitidas apenas para o host de desenvolvimento `10.0.2.2` (emulador Android) via `network_security_config` específico de debug
6. WHEN o usuário pressiona o botão voltar no Android e a WebView possui histórico de navegação, THE App_Flutter SHALL navegar para a página anterior na WebView
7. WHEN o usuário realiza o gesto swipe-to-go-back no iOS e a WebView possui histórico de navegação, THE App_Flutter SHALL navegar para a página anterior na WebView
8. IF a WebView não possui histórico de navegação quando o usuário aciona a navegação para trás (botão voltar no Android ou swipe-to-go-back no iOS), THEN THE App_Flutter SHALL permanecer na tela atual sem fechar o aplicativo

### Requisito 15: Conformidade com Políticas do Google Play

**User Story:** Como equipe de produto, eu quero que o aplicativo atenda aos requisitos do Google Play para publicação, para que o aplicativo não seja rejeitado por ser considerado apenas um wrapper de WebView.

#### Critérios de Aceitação

1. THE App_Flutter SHALL implementar pelo menos 5 telas nativas Flutter (login, cadastro, dashboard, listagem de simulados, perfil) que não utilizam WebView, onde cada tela contém pelo menos um widget interativo Flutter renderizado nativamente
2. THE App_Flutter SHALL utilizar a WebView_Simulado exclusivamente para a tela de realização de simulados (exibição de questões e registro de respostas), não sendo possível navegar para outras seções do aplicativo através da WebView
3. THE App_Flutter SHALL implementar navegação nativa (BottomNavigationBar, AppBar, rotas Flutter) em vez de depender de navegação web, de forma que todas as transições entre seções do aplicativo ocorram via rotas Flutter
4. THE App_Flutter SHALL utilizar componentes nativos Flutter para exibição de dados (listas, cards, gráficos) em vez de renderizar conteúdo web, em todas as telas fora da WebView_Simulado
5. THE App_Flutter SHALL implementar no mínimo 3 funcionalidades nativas de integração com o sistema operacional: notificações push, deep linking e armazenamento local offline
6. WHEN o usuário navega entre as seções do aplicativo (dashboard, listagem de simulados, perfil), THE App_Flutter SHALL realizar a transição utilizando rotas Flutter sem carregar nenhuma instância de WebView

### Requisito 16: Tela de Notificações Nativa

**User Story:** Como usuário, eu quero visualizar o histórico de notificações recebidas em uma tela nativa, para que eu possa acessar notificações anteriores que não li.

#### Critérios de Aceitação

1. THE App_Flutter SHALL exibir uma Tela_Nativa de notificações contendo a lista das últimas 100 notificações recebidas no dispositivo, ordenadas da mais recente para a mais antiga
2. THE App_Flutter SHALL armazenar as notificações localmente (sqflite ou Hive) já que a API_Laravel atualmente não expõe um endpoint de inbox de notificações; cada registro persistido SHALL conter título, corpo, URL de destino, data/hora de recebimento e estado de leitura
3. WHEN o usuário toca em uma notificação na lista, THE Router_App SHALL navegar para a tela correspondente ao conteúdo da notificação utilizando o Deep_Link_Handler (Requisito 11) e marcar a notificação como lida
4. THE Tela_Nativa de notificações SHALL diferenciar notificações não lidas das lidas através de um indicador visual distinto (destaque de fundo ou marcador) visível sem interação adicional
5. WHEN novas notificações são recebidas, THE App_Flutter SHALL atualizar o badge numérico no ícone de sino da AppBar definido no Requisito 10.8, exibindo a quantidade de não lidas e exibindo "99+" quando a contagem exceder 99
6. WHEN o usuário abre a tela de notificações, THE App_Flutter SHALL marcar todas as notificações visíveis na lista como lidas e atualizar o badge para zero
7. IF o armazenamento local atingir o limite de 100 notificações, THEN THE App_Flutter SHALL remover as notificações mais antigas para acomodar as novas
8. IF a notificação tocada não possuir uma URL de destino válida, THEN THE App_Flutter SHALL permanecer na tela atual sem realizar navegação
9. THE Tela_Nativa de notificações SHALL incluir um botão para abrir as configurações de notificação do sistema operacional, permitindo ao usuário desativar ou reativar notificações fora do aplicativo

### Requisito 17: Splash Screen e Inicialização

**User Story:** Como usuário, eu quero que o aplicativo inicie rapidamente com uma tela de apresentação, para que eu tenha feedback visual imediato ao abrir o aplicativo.

#### Critérios de Aceitação

1. WHEN o App_Flutter é iniciado, THE App_Flutter SHALL exibir uma splash screen nativa contendo o logotipo do Operação Alfa centralizado na tela
2. WHILE o App_Flutter está verificando o estado de autenticação, THE splash screen SHALL permanecer visível por no máximo 5 segundos
3. THE verificação do estado de autenticação SHALL consistir em: (a) recuperar o Sanctum_Bearer_Token do armazenamento seguro; (b) caso exista, validá-lo via GET /api/me da API_Laravel
4. WHEN GET /api/me retorna HTTP 200, THE Router_App SHALL navegar para a tela de dashboard
5. WHEN GET /api/me retorna HTTP 401 (token inválido ou revogado), THE Gerenciador_De_Sessão SHALL limpar o token armazenado e o Router_App SHALL navegar para a tela de login
6. WHEN não há Sanctum_Bearer_Token armazenado, THE Router_App SHALL navegar diretamente para a tela de login
7. THE splash screen SHALL utilizar o mecanismo nativo de cada plataforma (Android 12+ Splash Screen API e iOS LaunchScreen)
8. IF a verificação de autenticação falhar por erro de rede ou timeout após 5 segundos E houver token armazenado, THEN THE Router_App SHALL navegar para a tela de dashboard em modo offline (com banner de conectividade conforme Requisito 12.1) preservando a sessão até que a próxima requisição autenticada retorne 401

### Requisito 18: Cache Local e Performance

**User Story:** Como usuário, eu quero que o aplicativo carregue rapidamente e funcione de forma fluida, para que minha experiência não seja prejudicada por tempos de espera longos.

#### Critérios de Aceitação

1. THE App_Flutter SHALL implementar cache local de dados frequentemente acessados (lista de simulados de GET /api/exams, estatísticas de GET /api/performance/statistics, ranking de GET /api/ranking, perfil de GET /api/user/profile) e exibir esses dados em no máximo 200ms após a abertura da tela correspondente
2. WHEN dados em cache estão disponíveis, THE App_Flutter SHALL exibir os dados em cache em no máximo 200ms e iniciar uma atualização em background com dados da API_Laravel com timeout de 30 segundos
3. THE App_Flutter SHALL utilizar estratégia stale-while-revalidate para dados de listagem e estatísticas, considerando os dados como stale após 5 minutos desde a última sincronização bem-sucedida
4. THE WebView_Simulado SHALL manter cache de recursos estáticos (CSS, JS, imagens) respeitando os headers HTTP de cache do servidor, permitindo carregamento sem requisição de rede em acessos subsequentes enquanto o cache for válido
5. WHEN o cache local de dados de API (excluindo o cache da WebView gerenciado pelo sistema) excede 50MB de armazenamento, THE App_Flutter SHALL remover os dados mais antigos automaticamente até que o armazenamento utilizado fique abaixo de 40MB
6. IF a atualização em background da API_Laravel falhar ou exceder o timeout de 30 segundos, THEN THE App_Flutter SHALL manter os dados em cache exibidos e tentar nova atualização na próxima abertura da tela
7. WHEN o usuário realiza uma ação que altera dados no servidor, THE App_Flutter SHALL invalidar as entradas de cache conforme o seguinte mapa:
   - Conclusão de simulado (detectada conforme Requisito 6.6) invalida: GET /api/performance/statistics, GET /api/ranking, GET /api/ranking/my-position, GET /api/exams (para atualizar indicador de tentativas anteriores)
   - Atualização de perfil (PUT /api/user/profile) invalida: GET /api/user/profile e GET /api/me
   - Mudança de status de assinatura (após webhook Edduz refletido em GET /api/me) invalida: GET /api/me e GET /api/exams
   - Logout invalida: todas as entradas de cache do usuário

### Requisito 19: Tela de Planos de Assinatura

**User Story:** Como usuário, eu quero visualizar e contratar planos de assinatura para acessar simulados premium, para que eu possa desbloquear o conteúdo completo do aplicativo.

#### Critérios de Aceitação

1. THE App_Flutter SHALL exibir uma Tela_Nativa de planos com a lista de planos obtidos via endpoint GET /api/plans da API_Laravel, exibindo para cada plano: nome (`name`), descrição (`description`), preço (`price`), duração em dias (`durationDays`) e lista de funcionalidades (`features`)
2. THE Tela_Nativa de planos SHALL destacar visualmente o plano atualmente ativo do usuário com base no `subscription_status` e `subscription_platform_id` retornados por GET /api/me
3. WHEN o usuário seleciona um plano pago e toca em "Assinar", THE App_Flutter SHALL chamar POST /api/edduz/checkout para obter a URL de checkout da plataforma Edduz e abri-la em uma WebView dedicada de checkout (não a WebView_Simulado) ou no navegador externo conforme a UX definida
4. WHEN o checkout é finalizado e o webhook Edduz atualiza o `subscription_status` do usuário no servidor, THE App_Flutter SHALL detectar a mudança via polling ou refresh manual de GET /api/me e invalidar o cache conforme Requisito 18.7
5. THE Tela_Nativa de planos SHALL incluir botão "Restaurar compra" que dispara um refresh de GET /api/subscription/status para sincronizar o estado da assinatura
6. IF a chamada GET /api/plans falha, THEN THE Tela_Nativa de planos SHALL exibir mensagem de erro com botão para tentar novamente
7. THE Tela_Nativa de planos SHALL incluir opção de cancelar assinatura ativa que chama POST /api/subscription/cancel após confirmação explícita do usuário em diálogo modal

### Requisito 20: Exclusão de Conta (LGPD e Política do Google Play)

**User Story:** Como usuário, eu quero poder excluir minha conta diretamente pelo aplicativo, para que eu exerça meu direito de exclusão de dados conforme a LGPD e em conformidade com a política do Google Play vigente desde 2024.

#### Critérios de Aceitação

1. THE Tela_Nativa de perfil SHALL incluir um item "Excluir minha conta" claramente visível em uma seção de configurações
2. WHEN o usuário toca em "Excluir minha conta", THE App_Flutter SHALL exibir uma tela de confirmação descrevendo quais dados serão removidos, o caráter irreversível da operação, e exigir que o usuário digite a palavra "EXCLUIR" antes de habilitar o botão de confirmação
3. WHEN o usuário confirma a exclusão, THE App_Flutter SHALL chamar DELETE /api/user/account da API_Laravel
4. WHEN a exclusão é bem-sucedida (HTTP 200), THE Gerenciador_De_Sessão SHALL limpar o Sanctum_Bearer_Token e todos os caches locais conforme Requisito 18.7, e o Router_App SHALL navegar para a tela de login com mensagem de confirmação de conta excluída
5. IF a chamada DELETE /api/user/account falha por erro de rede, THEN THE App_Flutter SHALL exibir mensagem de erro mantendo o usuário logado e oferecer opção de tentar novamente
6. THE App_Flutter SHALL incluir, na tela de configurações ou na tela de planos, um link visível para a página web pública de solicitação de exclusão de conta no Domínio_Sistema, conforme exigência do Google Play para que usuários sem o app instalado possam solicitar exclusão

### Requisito 21: Privacidade, Segurança e Conformidade

**User Story:** Como equipe de produto, eu quero que o aplicativo atenda aos requisitos de privacidade e segurança esperados pelas lojas de aplicativos e pela LGPD, para que possamos publicar e manter o aplicativo nas lojas sem riscos de remoção.

#### Critérios de Aceitação

1. THE App_Flutter SHALL exibir, na primeira inicialização após instalação, uma tela com links para Política de Privacidade e Termos de Uso hospedados no Domínio_Sistema, exigindo aceite explícito antes de prosseguir para a tela de login
2. THE Tela_Nativa de perfil SHALL incluir links permanentes para Política de Privacidade e Termos de Uso
3. THE App_Flutter SHALL armazenar dados sensíveis (Sanctum_Bearer_Token, device_id) exclusivamente em flutter_secure_storage; dados não sensíveis podem usar SharedPreferences/SQLite local
4. THE App_Flutter SHALL declarar, no `manifest` do Google Play (Data Safety) e em `App Privacy` da App Store, exatamente os dados coletados: e-mail, nome, telefone (opcional), token FCM, identificador de dispositivo, dados de uso (estatísticas de simulados)
5. THE App_Flutter NÃO SHALL transmitir dados de uso, eventos analíticos ou logs de erro a serviços de terceiros sem que o serviço esteja declarado nas políticas de privacidade do critério 1
6. WHEN qualquer requisição da API_Laravel retorna um campo de erro contendo dados sensíveis, THE App_Flutter NÃO SHALL incluir o conteúdo da resposta em logs persistentes ou telemetria
7. THE App_Flutter SHALL incluir um mecanismo de versionamento do contrato com a API: caso a API retorne header `X-API-Min-Version` superior à versão suportada pelo cliente, o aplicativo SHALL exibir uma tela bloqueante orientando o usuário a atualizar pela loja correspondente

### Requisito 22: Histórico de Tentativas Nativo

**User Story:** Como usuário, eu quero ver o histórico das minhas tentativas de simulado em uma tela nativa, para que eu acompanhe meu progresso ao longo do tempo.

#### Critérios de Aceitação

1. THE App_Flutter SHALL exibir uma Tela_Nativa de histórico de tentativas com dados obtidos via GET /api/performance/history da API_Laravel
2. THE Tela_Nativa de histórico SHALL exibir cada tentativa com: título do simulado, data de realização, percentual de acerto e status (em andamento ou finalizada)
3. WHEN o usuário toca em uma tentativa finalizada, THE Router_App SHALL navegar para a tela de detalhes do simulado correspondente, exibindo a seção de resultado conforme Requisito 11.4
4. WHEN o usuário toca em uma tentativa em andamento, THE Router_App SHALL abrir a WebView_Simulado retomando a tentativa na URL_Tentativa_Pattern correspondente
5. THE Tela_Nativa de histórico SHALL ser acessível a partir do dashboard via atalho mencionado no Requisito 3.6
6. WHILE os dados estão sendo carregados, THE Tela_Nativa de histórico SHALL exibir indicadores de carregamento (shimmer/skeleton)
7. IF a chamada GET /api/performance/history retornar lista vazia, THEN THE Tela_Nativa de histórico SHALL exibir estado vazio orientando o usuário a iniciar seu primeiro simulado
8. IF a chamada GET /api/performance/history falhar, THEN THE Tela_Nativa de histórico SHALL exibir mensagem de erro com botão para tentar novamente

## Notas Técnicas e Dependências de Backend

Os seguintes itens foram identificados durante a análise dos requisitos como necessitando ajustes ou novas implementações no backend Laravel para que o App_Flutter possa atender aos requisitos plenamente. Eles estão listados aqui como dependências explícitas, não como requisitos do app.

1. **Paginação em GET /api/exams**: o endpoint atualmente retorna `Collection::all()` sem paginação. Para suportar listas grandes, o backend deve adicionar parâmetros `page` e `per_page` (padrão 20) e retornar metadados de paginação. Enquanto não implementado, o Requisito 4.4 prevê renderização virtualizada da lista completa.

2. **Upload de foto de perfil**: o `UpdateProfileRequest` atual aceita apenas `name`, `email`, `phone`, `password` (`app/Http/Requests/User/UpdateProfileRequest.php`). Para habilitar o item "foto de perfil editável" mencionado em iterações anteriores deste documento, o backend deve adicionar campo `profile_photo` na tabela `users`, endpoint dedicado `POST /api/user/profile/photo` aceitando multipart, e armazenamento em disco gerenciado. Enquanto não implementado, o Requisito 9.10 limita a tela de perfil a exibir um placeholder.

3. **Inbox de notificações**: o backend atualmente não persiste histórico de notificações enviadas via FCM (apenas registra tokens em `fcm_tokens`). O Requisito 16.2 mantém o histórico local-only no dispositivo. Caso seja necessário sincronizar inbox entre dispositivos, será preciso criar tabela `notifications` e endpoints `GET /api/notifications`, `POST /api/notifications/{id}/read`.

4. **URL de execução de simulado no React PWA**: existem dois padrões legados convivendo (`/simulado/:id/executar/:tentativaId` e `/simulado/:examId/tentativa/:attemptId`). O App_Flutter usará o padrão novo (`/tentativa/`) ao iniciar simulados, mas o URL_Tentativa_Pattern aceita ambos para compatibilidade com tentativas pré-existentes.

5. **Domínio em ESTRUTURA_URLS.md**: a documentação `laravel/ESTRUTURA_URLS.md` cita `operacaoalfa.com` (sem `.br`); o domínio correto, conforme `android/app/build.gradle.kts`, é `operacaoalfa.com.br`. A documentação Laravel deve ser atualizada para evitar divergência.

6. **Header `X-API-Min-Version`**: o Requisito 21.7 prevê um mecanismo de bloqueio por versão. O backend ainda não emite este header; é uma dependência futura para ativar atualização forçada.

7. **Endpoint público de exclusão de conta**: o Google Play exige uma URL pública (sem necessidade de instalar o app) para solicitar exclusão de conta. Esta página deve ser publicada no Domínio_Sistema antes da publicação na loja (Requisito 20.6).
