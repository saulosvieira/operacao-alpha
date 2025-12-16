# Erro Resolvido - "can't detect preamble"

## Data: 2024-12-15

## Problema Original
```
@vitejs/plugin-react can't detect preamble. Something is wrong.
toast.tsx:111
```

## Causa Raiz
O erro de "preamble" acontece quando o HMR (Hot Module Replacement) do Vite não consegue injetar o código de refresh do React antes do código JSX ser executado. Isso é um problema conhecido com o dev server do Vite em ambientes Docker.

## Solução Aplicada

### Abordagem: Usar Build de Produção

Em vez de tentar corrigir o dev server, optamos por usar o build de produção que não tem esse problema.

### 1. Alteração no vite.config.ts
- Mudança de `@vitejs/plugin-react` para `@vitejs/plugin-react-swc`
- Ordem dos plugins: react() antes de laravel()
- Configuração de manifest: `manifest: 'manifest.json'`

### 2. Correção de arquivos faltantes
- Criado `laravel/resources/react/mocks/data.ts` com dados mock
- Atualizado `Carreiras.tsx` para usar os novos nomes de propriedades

### 3. Build de produção
```bash
docker exec simulados-vite npm run build
```

### 4. Correção do manifest
O Vite 6 gera o manifest em `.vite/manifest.json`, mas o Laravel espera em `manifest.json`:
```bash
cp laravel/public/build/.vite/manifest.json laravel/public/build/manifest.json
```

### 5. Remoção do arquivo hot
```bash
rm laravel/public/hot
```

## Verificações Realizadas
- ✅ Build de produção bem sucedido
- ✅ HTTP 200 em http://localhost:8090
- ✅ Assets JavaScript e CSS carregando
- ✅ manifest.json PWA acessível
- ✅ sw.js acessível
- ✅ Ícones PWA acessíveis

## Configuração Final

### vite.config.ts
```typescript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react-swc';
import path from 'path';

export default defineConfig({
    plugins: [
        react(),
        laravel({
            input: ['resources/react/main.tsx', 'resources/react/index.css'],
            refresh: true,
        }),
    ],
    // ... resto da config
});
```

## Modo de Desenvolvimento

Para desenvolvimento futuro, você tem duas opções:

### Opção 1: Usar build de produção (recomendado para evitar o erro)
```bash
docker exec simulados-vite npm run build
```

### Opção 2: Usar dev server (pode ter o erro de preamble)
```bash
docker start simulados-vite
```

## Status
✅ RESOLVIDO - Aplicação funcionando com build de produção
