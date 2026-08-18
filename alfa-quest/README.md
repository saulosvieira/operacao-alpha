# Operação Alfa - App Flutter

Aplicativo híbrido Flutter (nativo + WebView) para a plataforma de simulados Operação Alfa.

## Requisitos

- Flutter 3.41+
- Dart 3.11+
- Android SDK 26+
- iOS 15+ (target)

## Setup

```bash
cd alfa-quest
flutter pub get
```

## Build

### Homologação

```bash
flutter run --flavor homolog \
  --dart-define=ENV=homolog \
  --dart-define=API_BASE_URL=https://operacao-alfa.homolog.mydev.com.br \
  --dart-define=APP_VERSION=1.0.0+5
```

### Produção

```bash
flutter build appbundle --flavor prod \
  --dart-define=ENV=prod \
  --dart-define=API_BASE_URL=https://operacaoalfa.com.br \
  --dart-define=APP_VERSION=1.0.0+5
```

### APK (debug)

```bash
flutter build apk --flavor prod \
  --dart-define=ENV=prod \
  --dart-define=API_BASE_URL=https://operacaoalfa.com.br \
  --dart-define=APP_VERSION=1.0.0+5
```

## Arquitetura

O app segue 4 camadas:

1. **Apresentação** - Widgets Flutter (Material Design 3)
2. **Aplicação** - Providers Riverpod
3. **Domínio** - Managers e Repositórios (SWR cache)
4. **Infraestrutura** - dio, drift, flutter_secure_storage, webview_flutter

### Estrutura de Pastas

```
lib/
├── main.dart
├── core/
│   ├── config/       # AppConfig (env vars)
│   ├── theme/        # Material Design 3 theme
│   ├── network/      # ApiClient (dio) + interceptors
│   ├── storage/      # SecureStorage, Database (drift)
│   ├── cache/        # CacheManager (SWR, 50MB LRU)
│   ├── errors/       # AppException hierarchy
│   └── utils/        # URL patterns
├── features/
│   ├── auth/         # Splash, Login, Register, Onboarding
│   ├── dashboard/    # Dashboard stats
│   ├── exams/        # List, Detail, WebView
│   ├── profile/      # Profile, Delete Account
│   ├── ranking/      # Ranking list
│   ├── notifications/ # Inbox
│   ├── plans/        # Subscription plans
│   ├── history/      # Attempt history
│   ├── connectivity/ # Offline banner
│   ├── shell/        # HomeShell (BottomNav)
│   └── system/       # Force Update
├── routing/          # GoRouter + DeepLinks
├── services/         # Auth, Session, FCM, Connectivity
└── data/
    ├── models/       # Freezed DTOs
    └── repositories/ # SWR repositories
```

## Features

- **Login/Cadastro nativo** com Sanctum Bearer Token
- **WebView para simulados** (React PWA) com injeção de token
- **Cache SWR** com drift/SQLite (50MB, LRU eviction)
- **Push notifications** via FCM
- **Deep links** (App Links Android + Universal Links iOS)
- **Offline mode** com banner e cache
- **Force update** via header X-API-Min-Version

## Configuração Firebase

Substituir os google-services.json placeholders em:
- `android/app/src/homolog/google-services.json`
- `android/app/src/prod/google-services.json`

Para iOS, colocar GoogleService-Info.plist em:
- `ios/Runner/Firebase/Homolog/GoogleService-Info.plist`
- `ios/Runner/Firebase/Prod/GoogleService-Info.plist`

## Package

- Android: `br.com.operacaoalfa.app`
- iOS Bundle ID: `br.com.operacaoalfa.app`
- versionCode: 5 (Play Store tem 4)
- versionName: 1.0.0
