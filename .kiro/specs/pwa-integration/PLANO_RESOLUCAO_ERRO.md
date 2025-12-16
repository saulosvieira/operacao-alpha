# Plano de Resolução - Erro "can't detect preamble"

## Resumo do Problema

**Erro**: `@vitejs/plugin-react can't detect preamble. Something is wrong.`
**Arquivo**: `toast.tsx:111`
**Sintoma**: Página carrega em branco, apenas título aparece
**Status**: Erro persiste mesmo após múltiplas tentativas de correção

## Tentativas Realizadas (Sem Sucesso)

1. ✗ Mudança de `@vitejs/plugin-react-swc` para `@vitejs/plugin-react`
2. ✗ Adição de `"use client"` no topo do toast.tsx
3. ✗ Configuração de `jsxRuntime: 'automatic'`
4. ✗ Exclusão de dependências do `optimizeDeps`
5. ✗ Desabilitação do Fast Refresh
6. ✗ Limpeza de cache múltiplas vezes
7. ✗ Teste em modo anônimo do navegador

## Diagnóstico

O erro indica que o plugin React não consegue processar corretamente o arquivo `toast.tsx`. Isso geralmente acontece quando:

1. Há incompatibilidade entre versões de pacotes
2. O arquivo tem alguma sintaxe que o plugin não reconhece
3. Há problema na configuração do TypeScript
4. O arquivo foi corrompido durante a migração

## Plano de Ação para Novo Chat

### Opção 1: Verificar Projeto Original (RECOMENDADO)

**Objetivo**: Confirmar se o projeto alfa-quest original funciona

```bash
# 1. Testar projeto original
cd alfa-quest
npm install
npm run dev

# 2. Abrir http://localhost:5173
# 3. Verificar se carrega sem erros
```

**Se funcionar**: O problema está na migração para Laravel
**Se não funcionar**: O problema está no código fonte original

### Opção 2: Comparar Arquivos

**Objetivo**: Identificar diferenças entre original e migrado

```bash
# Comparar toast.tsx
diff alfa-quest/src/components/ui/toast.tsx laravel/resources/react/components/ui/toast.tsx

# Comparar vite.config
diff alfa-quest/vite.config.ts laravel/vite.config.ts

# Comparar package.json
diff alfa-quest/package.json laravel/package.json
```

### Opção 3: Reconstruir do Zero

**Objetivo**: Migrar novamente com abordagem diferente

**Passos**:
1. Backup do laravel/resources/react atual
2. Copiar alfa-quest/src para laravel/resources/react (fresh)
3. Usar vite.config.ts do alfa-quest como base
4. Ajustar apenas paths necessários
5. Testar incrementalmente

### Opção 4: Usar Build de Produção

**Objetivo**: Evitar o erro usando build estático

```bash
# No projeto alfa-quest original
cd alfa-quest
npm run build

# Copiar build para Laravel
cp -r dist/* ../laravel/public/

# Desabilitar Vite dev server
# Servir apenas arquivos estáticos
```

## Informações para o Novo Chat

### Contexto Essencial

**Estrutura do Projeto**:
- Laravel backend em `laravel/`
- React frontend em `laravel/resources/react/`
- Vite dev server em container Docker `simulados-vite`
- Projeto original em `alfa-quest/`

**Erro Específico**:
```
toast.tsx:111 Uncaught Error: @vitejs/plugin-react can't detect preamble. 
Something is wrong.
```

**Configuração Atual**:
- Vite 6.4.1
- React 18
- @vitejs/plugin-react (não swc)
- TypeScript
- Docker Compose

### Arquivos Críticos

1. `laravel/vite.config.ts` - Configuração do Vite
2. `laravel/resources/react/components/ui/toast.tsx` - Arquivo com erro
3. `laravel/package.json` - Dependências
4. `laravel/tsconfig.json` - Configuração TypeScript
5. `alfa-quest/vite.config.ts` - Config original (referência)

### Comandos Úteis

```bash
# Ver logs do Vite
docker logs simulados-vite --tail 50

# Reiniciar Vite
docker restart simulados-vite

# Limpar cache
docker exec simulados-vite rm -rf /var/www/laravel/node_modules/.vite

# Testar projeto original
cd alfa-quest && npm run dev
```

## Próximos Passos Sugeridos

### Passo 1: Validar Projeto Original
Confirmar que `alfa-quest/` funciona sem erros

### Passo 2: Identificar Diferença
Comparar configurações e arquivos entre original e migrado

### Passo 3: Solução Baseada no Diagnóstico

**Se problema for na migração**:
- Refazer migração com cuidado
- Copiar configs do original
- Ajustar apenas paths

**Se problema for no código original**:
- Atualizar dependências
- Corrigir sintaxe do toast.tsx
- Usar versão compatível do plugin

**Se problema for no ambiente**:
- Usar build de produção
- Desabilitar dev server
- Servir arquivos estáticos

## Solução Temporária (Workaround)

Enquanto não resolvemos o erro, você pode:

1. **Usar o projeto original** (`alfa-quest/`) para desenvolvimento
2. **Fazer build de produção** e copiar para Laravel
3. **Desabilitar o componente Toast** temporariamente
4. **Usar versão anterior** do código (se houver no git)

## Informações Técnicas Adicionais

**Versões de Pacotes** (verificar com `npm list`):
- vite: 6.4.1
- @vitejs/plugin-react: (verificar versão instalada)
- react: 18.x
- typescript: (verificar versão)

**Ambiente**:
- OS: macOS
- Docker: Sim
- Node.js: 20.x (no container)

**URLs**:
- App: http://localhost:8090
- Vite Dev: http://localhost:5173

## Pergunta Inicial para Novo Chat

"Estou com um erro persistente no Vite + React + Laravel. O erro é '@vitejs/plugin-react can't detect preamble' no arquivo toast.tsx. A página carrega em branco. Já tentei múltiplas soluções sem sucesso. Tenho um projeto original (alfa-quest/) que preciso migrar para Laravel. Qual a melhor abordagem para resolver isso? Posso fornecer mais detalhes sobre as tentativas anteriores."

---

**Arquivo criado em**: 2024-12-15
**Task relacionada**: 24.2 - Validar PWA com Lighthouse
**Status**: Bloqueado por erro de build

