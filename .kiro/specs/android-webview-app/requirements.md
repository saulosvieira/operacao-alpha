# Documento de Requisitos

## Introdução

Este documento define os requisitos para o aplicativo Android "Operação Alfa" que embarca uma WebView do sistema web de simulados existente. O aplicativo carrega o frontend React/Laravel atual, mantém sessões de login persistentes e permite que os usuários recebam notificações push via Firebase Cloud Messaging (FCM), integrando-se com o backend de notificações já existente.

## Glossário

- **App_Android**: O aplicativo Android nativo que hospeda a WebView e gerencia notificações push
- **WebView**: O componente Android que renderiza o conteúdo web do frontend existente dentro do aplicativo nativo
- **FCM_Service**: O serviço Firebase Cloud Messaging responsável por receber e exibir notificações push no dispositivo Android
- **Gerenciador_De_Sessão**: O componente responsável por persistir cookies e dados de localStorage entre sessões do aplicativo
- **Gerenciador_De_Conectividade**: O componente que monitora o estado da conexão de rede e exibe feedback ao usuário
- **Gerenciador_De_Navegação**: O componente que controla a navegação para trás dentro da WebView e do aplicativo
- **Gerenciador_De_Arquivos**: O componente que lida com uploads de arquivos originados do conteúdo web na WebView
- **Backend_Notificações**: O sistema backend Laravel existente que gerencia inscrições de notificações e envio via Web Push API/VAPID
- **Splash_Screen**: A tela de carregamento exibida enquanto a WebView inicializa o conteúdo web

## Requisitos

### Requisito 1: Carregamento do Frontend na WebView

**User Story:** Como usuário, eu quero acessar o sistema de simulados pelo aplicativo Android, para que eu tenha uma experiência nativa sem precisar abrir o navegador.

#### Critérios de Aceitação

1. WHEN o App_Android é iniciado, THE WebView SHALL carregar a URL base do frontend do sistema de simulados
2. THE WebView SHALL habilitar JavaScript para garantir o funcionamento completo do frontend React
3. THE WebView SHALL habilitar armazenamento DOM (localStorage e sessionStorage) para que o frontend funcione corretamente
4. WHEN o conteúdo web contém links externos (fora do domínio do sistema), THE App_Android SHALL abrir esses links no navegador padrão do dispositivo
5. WHEN o conteúdo web contém links internos (dentro do domínio do sistema), THE WebView SHALL navegar internamente sem abrir o navegador externo
6. WHILE a WebView está carregando o conteúdo inicial, THE App_Android SHALL exibir uma Splash_Screen com a identidade visual do aplicativo

### Requisito 2: Persistência de Sessão e Login

**User Story:** Como usuário, eu quero permanecer logado no aplicativo entre sessões, para que eu não precise inserir minhas credenciais toda vez que abrir o aplicativo.

#### Critérios de Aceitação

1. THE Gerenciador_De_Sessão SHALL persistir cookies de autenticação entre reinicializações do aplicativo
2. THE Gerenciador_De_Sessão SHALL habilitar o armazenamento de cookies de terceiros quando necessário para o fluxo de autenticação
3. THE Gerenciador_De_Sessão SHALL manter os dados de localStorage e sessionStorage do frontend entre sessões do aplicativo
4. WHEN o usuário realiza logout pelo frontend web, THE Gerenciador_De_Sessão SHALL limpar todos os cookies e dados de sessão armazenados
5. THE WebView SHALL utilizar um banco de dados de cookies persistente em vez de cookies apenas em memória

### Requisito 3: Notificações Push via Firebase Cloud Messaging

**User Story:** Como usuário, eu quero receber notificações push no meu dispositivo Android, para que eu seja informado sobre novos simulados, resultados e atualizações de ranking.

#### Critérios de Aceitação

1. WHEN o App_Android é iniciado pela primeira vez, THE FCM_Service SHALL solicitar permissão de notificação ao usuário (Android 13+)
2. WHEN o usuário concede permissão de notificação, THE FCM_Service SHALL obter um token de registro FCM do dispositivo
3. WHEN um token FCM é obtido, THE App_Android SHALL enviar o token ao Backend_Notificações através do endpoint de inscrição existente (POST /api/notifications/subscribe)
4. WHEN uma notificação push é recebida pelo FCM_Service, THE App_Android SHALL exibir uma notificação do sistema com título, corpo e ícone
5. WHEN o usuário toca em uma notificação, THE App_Android SHALL abrir o aplicativo e navegar a WebView para a URL especificada nos dados da notificação
6. WHEN o token FCM é atualizado pelo Firebase, THE FCM_Service SHALL enviar o novo token ao Backend_Notificações e remover o token antigo
7. IF o envio do token FCM ao backend falhar, THEN THE App_Android SHALL armazenar o token localmente e tentar reenviar na próxima inicialização

### Requisito 4: Navegação e Botão Voltar

**User Story:** Como usuário, eu quero usar o botão voltar do Android para navegar no histórico de páginas do sistema, para que a navegação seja intuitiva como em um navegador.

#### Critérios de Aceitação

1. WHEN o usuário pressiona o botão voltar do dispositivo e a WebView possui histórico de navegação, THE Gerenciador_De_Navegação SHALL navegar para a página anterior na WebView
2. WHEN o usuário pressiona o botão voltar do dispositivo e a WebView não possui histórico de navegação, THE App_Android SHALL exibir um diálogo de confirmação antes de fechar o aplicativo
3. WHEN o usuário confirma o fechamento no diálogo, THE App_Android SHALL encerrar a atividade principal
4. WHEN o usuário cancela o fechamento no diálogo, THE App_Android SHALL permanecer na tela atual

### Requisito 5: Upload de Arquivos

**User Story:** Como usuário, eu quero poder enviar arquivos (como fotos de perfil ou documentos) pelo aplicativo, para que eu tenha a mesma funcionalidade disponível no navegador web.

#### Critérios de Aceitação

1. WHEN o conteúdo web solicita um upload de arquivo, THE Gerenciador_De_Arquivos SHALL abrir o seletor de arquivos nativo do Android
2. WHEN o usuário seleciona um arquivo no seletor, THE Gerenciador_De_Arquivos SHALL enviar o arquivo selecionado para a WebView
3. WHEN o usuário cancela a seleção de arquivo, THE Gerenciador_De_Arquivos SHALL notificar a WebView que nenhum arquivo foi selecionado
4. THE Gerenciador_De_Arquivos SHALL suportar seleção de imagens da câmera e da galeria do dispositivo

### Requisito 6: Tratamento de Conectividade de Rede

**User Story:** Como usuário, eu quero ser informado quando não houver conexão com a internet, para que eu entenda por que o conteúdo não está carregando.

#### Critérios de Aceitação

1. WHEN a WebView falha ao carregar uma página por falta de conexão de rede, THE Gerenciador_De_Conectividade SHALL exibir uma tela de erro amigável com uma mensagem informando a ausência de conexão
2. THE tela de erro SHALL incluir um botão "Tentar Novamente" para recarregar a página
3. WHEN o usuário pressiona o botão "Tentar Novamente", THE WebView SHALL tentar carregar novamente a última URL solicitada
4. WHEN a conexão de rede é restaurada enquanto a tela de erro está visível, THE Gerenciador_De_Conectividade SHALL recarregar automaticamente a última URL solicitada

### Requisito 7: Configuração e Build do Projeto Android

**User Story:** Como desenvolvedor, eu quero que o projeto Android esteja configurado corretamente com todas as dependências necessárias, para que eu possa compilar e distribuir o aplicativo.

#### Critérios de Aceitação

1. THE App_Android SHALL utilizar o projeto Android existente na pasta `android/` com o pacote `br.com.operacaoalfa`
2. THE App_Android SHALL declarar as permissões INTERNET e ACCESS_NETWORK_STATE no AndroidManifest.xml
3. THE App_Android SHALL declarar a permissão POST_NOTIFICATIONS para Android 13 (API 33) e superior
4. THE App_Android SHALL configurar a dependência do Firebase Cloud Messaging no build.gradle
5. THE App_Android SHALL configurar a URL base do frontend como uma constante configurável (BuildConfig ou recurso de string)
6. THE App_Android SHALL utilizar `usesCleartextTraffic=true` apenas em builds de debug para permitir testes com HTTP local
7. THE App_Android SHALL configurar o `networkSecurityConfig` para permitir conexões seguras ao domínio do sistema em produção

### Requisito 8: Integração JavaScript-Nativo

**User Story:** Como desenvolvedor, eu quero que o aplicativo Android possa se comunicar com o frontend web, para que funcionalidades nativas como notificações push possam ser integradas ao fluxo do frontend.

#### Critérios de Aceitação

1. THE App_Android SHALL expor uma interface JavaScript (JavaScriptInterface) para a WebView que permita ao frontend solicitar o token FCM do dispositivo
2. WHEN o frontend solicita o token FCM via interface JavaScript, THE App_Android SHALL retornar o token FCM atual do dispositivo
3. THE App_Android SHALL expor um método na interface JavaScript que permita ao frontend verificar se o aplicativo está rodando dentro da WebView nativa
4. WHEN o frontend detecta que está rodando na WebView nativa, THE frontend SHALL utilizar a interface JavaScript para obter o token FCM em vez de usar a Web Push API do service worker
