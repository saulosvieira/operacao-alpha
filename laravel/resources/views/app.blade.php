<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Operação ALFA - Simulados Militares</title>
    <meta name="description" content="Prepare-se para concursos militares com simulados completos, ranking em tempo real e análise de desempenho. Polícia Militar, Bombeiros, Exército e mais." />
    <meta name="author" content="Operação ALFA" />

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#1e40af" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="default" />
    <meta name="apple-mobile-web-app-title" content="Alfa Quest" />
    <meta name="mobile-web-app-capable" content="yes" />
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json" />
    
    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png" />
    <link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-152x152.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/icon-192x192.png" />
    <link rel="apple-touch-icon" sizes="167x167" href="/icons/icon-192x192.png" />

    <!-- Open Graph -->
    <meta property="og:title" content="Operação ALFA - Simulados Militares" />
    <meta property="og:description" content="Prepare-se para concursos militares com simulados completos, ranking em tempo real e análise de desempenho." />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="https://lovable.dev/opengraph-image-p98pqg.png" />

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="@lovable_dev" />
    <meta name="twitter:image" content="https://lovable.dev/opengraph-image-p98pqg.png" />

    <!-- Vite Assets -->
    @viteReactRefresh
    @vite(['resources/react/main.tsx', 'resources/react/index.css'])
  </head>

  <body>
    <div id="root"></div>
  </body>
</html>
