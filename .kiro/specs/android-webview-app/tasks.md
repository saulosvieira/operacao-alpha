# Plano de Implementação: Android WebView App — Operação Alfa

## Visão Geral

Implementação do aplicativo Android que encapsula o frontend web existente em uma WebView nativa, com sessões persistentes, notificações push via FCM e integração com o backend Laravel. O plano está dividido em: configuração do projeto Android, implementação da WebView e componentes nativos, e criação dos novos componentes backend para o canal FCM.

## Tarefas

- [x] 1. Configurar dependências e permissões do projeto Android
  - [x] 1.1 Adicionar dependências do Firebase e WebView ao version catalog e build.gradle
    - Adicionar Firebase BOM, Firebase Messaging e Activity KTX ao `android/gradle/libs.versions.toml`
    - Adicionar plugin `com.google.gms.google-services` ao `android/build.gradle.kts` (project-level) e `android/app/build.gradle.kts` (app-level)
    - Habilitar `buildFeatures { viewBinding = true }` no `android/app/build.gradle.kts`
    - Adicionar placeholder para `google-services.json` (arquivo será fornecido pelo desenvolvedor)
    - _Requisitos: 7.4_

  - [x] 1.2 Configurar AndroidManifest.xml com permissões e componentes
    - Adicionar permissões: `INTERNET`, `ACCESS_NETWORK_STATE`, `POST_NOTIFICATIONS`
    - Configurar `usesCleartextTraffic` condicional via `networkSecurityConfig`
    - Declarar `MainActivity` como launcher activity com `android:exported="true"`
    - Declarar `FCMService` com intent-filter para `com.google.firebase.MESSAGING_EVENT`
    - _Requisitos: 7.2, 7.3, 7.6, 7.7_

  - [x] 1.3 Criar arquivo de configuração de segurança de rede
    - Criar `android/app/src/main/res/xml/network_security_config.xml`
    - Permitir cleartext apenas para `10.0.2.2` (emulador) em debug
    - Configurar domínio de produção com HTTPS obrigatório
    - _Requisitos: 7.6, 7.7_

  - [x] 1.4 Adicionar URL base como recurso de string configurável
    - Adicionar `base_url` em `android/app/src/main/res/values/strings.xml`
    - Configurar BuildConfig field para URL base via `buildConfigField` no `build.gradle.kts`
    - _Requisitos: 7.5_

- [x] 2. Checkpoint — Verificar que o projeto compila sem erros
  - Garantir que todas as dependências resolvem corretamente e o projeto compila. Perguntar ao usuário se há dúvidas.

- [x] 3. Implementar MainActivity com WebView e splash screen
  - [x] 3.1 Criar layouts XML da MainActivity
    - Criar `activity_main.xml` com FrameLayout contendo: WebView, splash screen (ImageView/ProgressBar) e tela de erro offline (TextView + botão "Tentar Novamente")
    - A splash screen deve exibir o ícone do app e um indicador de carregamento
    - A tela offline deve ter mensagem amigável e botão de retry
    - _Requisitos: 1.6, 6.1, 6.2_

  - [x] 3.2 Implementar MainActivity com configuração da WebView
    - Criar `MainActivity.kt` em `android/app/src/main/java/br/com/operacaoalfa/`
    - Configurar `WebSettings`: JavaScript habilitado, DOM Storage habilitado, cookies habilitados
    - Configurar `CookieManager` com `setAcceptCookie(true)` e `setAcceptThirdPartyCookies(true)`
    - Exibir splash screen ao iniciar e esconder quando `onPageFinished` for chamado
    - Carregar a URL base configurada no recurso de string
    - _Requisitos: 1.1, 1.2, 1.3, 1.6, 2.1, 2.2, 2.3, 2.5_

  - [x] 3.3 Implementar tratamento do botão voltar
    - Se a WebView tem histórico (`canGoBack()`), navegar para trás
    - Se não tem histórico, exibir `AlertDialog` de confirmação antes de fechar
    - Usar `onBackPressedDispatcher` com callback
    - _Requisitos: 4.1, 4.2, 4.3, 4.4_

  - [x] 3.4 Implementar persistência de sessão no ciclo de vida
    - Chamar `CookieManager.getInstance().flush()` em `onPause()` e `onDestroy()`
    - Garantir que cookies sobrevivem a reinicializações do app
    - _Requisitos: 2.1, 2.5_

- [x] 4. Implementar WebViewClient e WebChromeClient customizados
  - [x] 4.1 Criar AppWebViewClient
    - Criar `AppWebViewClient.kt` em `android/app/src/main/java/br/com/operacaoalfa/`
    - Implementar `shouldOverrideUrlLoading`: URLs do domínio base → navegação interna; URLs externas → `Intent(ACTION_VIEW)` no browser
    - Implementar `onReceivedError` e `onReceivedHttpError`: exibir tela offline via callback
    - Implementar `onPageFinished`: esconder splash screen via callback
    - Implementar `onReceivedSslError`: aceitar certificados em debug builds apenas (usando `BuildConfig.DEBUG`)
    - _Requisitos: 1.4, 1.5, 1.6, 6.1_

  - [x] 4.2 Criar AppWebChromeClient
    - Criar `AppWebChromeClient.kt` em `android/app/src/main/java/br/com/operacaoalfa/`
    - Implementar `onShowFileChooser`: delegar para `ActivityResultLauncher` na MainActivity
    - Suportar seleção de imagens (câmera e galeria) e arquivos genéricos
    - _Requisitos: 5.1, 5.2, 5.3, 5.4_

  - [x] 4.3 Implementar upload de arquivos na MainActivity
    - Registrar `ActivityResultLauncher<Intent>` para `StartActivityForResult`
    - No callback, enviar URI selecionada para `fileUploadCallback` da WebView
    - Tratar cancelamento enviando `null` ao callback
    - _Requisitos: 5.1, 5.2, 5.3_

- [x] 5. Implementar NetworkMonitor e tela offline
  - [x] 5.1 Criar NetworkMonitor
    - Criar `NetworkMonitor.kt` em `android/app/src/main/java/br/com/operacaoalfa/`
    - Usar `ConnectivityManager.registerDefaultNetworkCallback` para monitorar conectividade
    - Expor propriedade `isOnline` e callback `onConnectivityChanged`
    - _Requisitos: 6.1, 6.4_

  - [x] 5.2 Integrar NetworkMonitor com MainActivity
    - Registrar o monitor em `onCreate`, desregistrar em `onDestroy`
    - Quando conexão é restaurada e tela offline está visível, recarregar automaticamente a última URL
    - Botão "Tentar Novamente" recarrega a WebView
    - _Requisitos: 6.2, 6.3, 6.4_

- [x] 6. Implementar NativeAppInterface (JavaScriptInterface)
  - [x] 6.1 Criar NativeAppInterface
    - Criar `NativeAppInterface.kt` em `android/app/src/main/java/br/com/operacaoalfa/`
    - Implementar `@JavascriptInterface fun isNativeApp(): Boolean` → retorna `true`
    - Implementar `@JavascriptInterface fun getFcmToken(): String` → retorna token FCM do SharedPreferences ou string vazia
    - Implementar `@JavascriptInterface fun getAppVersion(): String` → retorna `BuildConfig.VERSION_NAME`
    - _Requisitos: 8.1, 8.2, 8.3_

  - [x] 6.2 Registrar NativeAppInterface na WebView
    - Adicionar `webView.addJavascriptInterface(NativeAppInterface(this), "NativeApp")` na configuração da WebView em MainActivity
    - _Requisitos: 8.1, 8.3, 8.4_

- [x] 7. Checkpoint — Verificar WebView funcional
  - Garantir que a WebView carrega o frontend, navegação funciona, splash screen aparece e desaparece, tela offline funciona, e upload de arquivos opera corretamente. Perguntar ao usuário se há dúvidas.

- [x] 8. Implementar FCMService e notificações push no Android
  - [x] 8.1 Criar FCMService
    - Criar `FCMService.kt` em `android/app/src/main/java/br/com/operacaoalfa/`
    - Estender `FirebaseMessagingService`
    - Implementar `onNewToken`: salvar token no SharedPreferences, marcar como não sincronizado, tentar enviar ao backend
    - Implementar `onMessageReceived`: extrair `title`, `body`, `url` dos dados da mensagem e chamar `showNotification`
    - Implementar `showNotification`: criar `NotificationCompat.Builder` com canal de notificação, PendingIntent para abrir MainActivity com a URL
    - _Requisitos: 3.2, 3.4, 3.5, 3.6_

  - [x] 8.2 Criar canal de notificação e gerenciar permissões
    - Criar `NotificationChannel` com ID e nome em `onCreate` da MainActivity (API 26+)
    - Solicitar permissão `POST_NOTIFICATIONS` em runtime para API 33+ usando `ActivityResultLauncher<String>`
    - _Requisitos: 3.1_

  - [x] 8.3 Implementar envio do token FCM ao backend
    - Criar função utilitária que faz `POST /api/notifications/fcm/subscribe` com `token` e `device_id`
    - Usar `HttpURLConnection` ou `OkHttp` (se já disponível) para a requisição HTTP
    - Gerar `device_id` como UUID persistido no SharedPreferences na primeira instalação
    - Obter token de autenticação dos cookies da WebView (`CookieManager`) para autenticar a requisição
    - Se falhar, marcar `fcm_token_synced = false` no SharedPreferences para retry na próxima inicialização
    - _Requisitos: 3.3, 3.6, 3.7_

  - [x] 8.4 Implementar navegação por notificação
    - Configurar `PendingIntent` no `showNotification` para abrir `MainActivity` com extra `notification_url`
    - Em `MainActivity.onCreate` e `onNewIntent`, verificar se há extra `notification_url` e carregar na WebView
    - _Requisitos: 3.5_

- [x] 9. Checkpoint — Verificar componentes Android completos
  - Garantir que o app compila, FCMService está registrado, permissões são solicitadas, e a estrutura de notificações está pronta. Perguntar ao usuário se há dúvidas.

- [x] 10. Implementar componentes backend para canal FCM
  - [x] 10.1 Criar migration para tabela `fcm_tokens`
    - Criar migration em `laravel/database/migrations/` com colunas: `id`, `user_id` (FK → users), `token` (text), `device_id` (string 255, unique), `created_at`, `updated_at`
    - Adicionar índice em `user_id`
    - _Requisitos: 3.3_

  - [x] 10.2 Criar modelo FcmToken
    - Criar `FcmToken.php` em `laravel/app/Domain/Notification/Models/`
    - Definir `$fillable = ['user_id', 'token', 'device_id']`
    - Definir relação `user(): BelongsTo`
    - _Requisitos: 3.3_

  - [x] 10.3 Criar FcmSubscribeAction e FcmUnsubscribeAction
    - Criar `FcmSubscribeAction.php` em `laravel/app/Domain/Notification/Actions/`
    - Usar `updateOrCreate` com `device_id` como chave para evitar duplicatas
    - Criar `FcmUnsubscribeAction.php` para remover token por `device_id` ou `token`
    - _Requisitos: 3.3, 3.6_

  - [x] 10.4 Criar FcmSendNotificationAction
    - Criar `FcmSendNotificationAction.php` em `laravel/app/Domain/Notification/Actions/`
    - Implementar `execute(string $userId, NotificationData $notification): array` — envia para todos os tokens FCM do usuário
    - Implementar `sendToAll(NotificationData $notification): array` — envia para todos os tokens FCM registrados
    - Usar HTTP v1 API do Firebase via Guzzle (`POST https://fcm.googleapis.com/v1/projects/{project}/messages:send`)
    - Enviar como `data message` (não `notification message`) para controle total no app
    - Remover tokens inválidos/expirados automaticamente (erro `UNREGISTERED`)
    - Reutilizar `NotificationData` DTO existente
    - _Requisitos: 3.4_

  - [x] 10.5 Criar FcmNotificationController com rotas
    - Criar `FcmNotificationController.php` em `laravel/app/Http/Controllers/Api/Notification/`
    - Implementar `subscribe(Request $request)`: validar `token` (required|string) e `device_id` (required|string), chamar `FcmSubscribeAction`
    - Implementar `unsubscribe(Request $request)`: validar `device_id` (required|string), chamar `FcmUnsubscribeAction`
    - Adicionar rotas em `laravel/routes/api/notifications.php`:
      - `POST /notifications/fcm/subscribe` (auth:sanctum)
      - `POST /notifications/fcm/unsubscribe` (auth:sanctum)
    - _Requisitos: 3.3, 3.6_

  - [ ]* 10.6 Escrever testes para FcmSubscribeAction e FcmUnsubscribeAction
    - Testar criação de novo token FCM
    - Testar atualização de token existente (mesmo device_id)
    - Testar remoção de token
    - _Requisitos: 3.3, 3.6_

  - [ ]* 10.7 Escrever testes para FcmNotificationController
    - Testar endpoint subscribe com dados válidos e inválidos
    - Testar endpoint unsubscribe
    - Testar que rotas requerem autenticação
    - _Requisitos: 3.3, 3.6_

- [x] 11. Integrar envio FCM ao fluxo de notificações existente
  - [x] 11.1 Atualizar SendNotificationAction para acionar canal FCM
    - Modificar `SendNotificationAction::execute()` para também chamar `FcmSendNotificationAction::execute()` após enviar via Web Push
    - Modificar `SendNotificationAction::sendToAll()` para também chamar `FcmSendNotificationAction::sendToAll()`
    - Agregar resultados de ambos os canais no retorno
    - _Requisitos: 3.4_

  - [x] 11.2 Adicionar configuração do Firebase no Laravel
    - Adicionar variáveis de ambiente no `.env.example`: `FIREBASE_PROJECT_ID`, `FIREBASE_CREDENTIALS_PATH` (caminho para o JSON de service account)
    - Adicionar configuração em `config/services.php` para Firebase
    - _Requisitos: 3.4_

  - [ ]* 11.3 Escrever testes para integração do canal FCM no SendNotificationAction
    - Testar que `execute` envia para ambos os canais (Web Push + FCM)
    - Testar que `sendToAll` envia para ambos os canais
    - Testar que falha em um canal não impede envio no outro
    - _Requisitos: 3.4_

- [x] 12. Checkpoint final — Verificar integração completa
  - Garantir que todos os testes passam, que as rotas FCM estão registradas, que o SendNotificationAction envia para ambos os canais, e que o app Android compila sem erros. Perguntar ao usuário se há dúvidas.

## Notas

- Tarefas marcadas com `*` são opcionais e podem ser puladas para um MVP mais rápido
- Cada tarefa referencia requisitos específicos para rastreabilidade
- Checkpoints garantem validação incremental
- O arquivo `google-services.json` do Firebase deve ser fornecido pelo desenvolvedor e colocado em `android/app/`
- A autenticação das requisições FCM do app Android ao backend usa os cookies de sessão da WebView (mesmo token de autenticação do frontend)
