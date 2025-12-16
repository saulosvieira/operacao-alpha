# Lighthouse Troubleshooting Guide

## Problemas Corrigidos ✅

### 1. Erro no Console: "@vitejs/plugin-react-swc can't detect preamble"

**Problema**: O plugin react-swc estava com configuração incorreta.

**Solução Aplicada**:
```typescript
// vite.config.ts
react({
    jsxRuntime: 'automatic',
})
```

### 2. Erro CSS: "@import must precede all other statements"

**Problema**: O @import do Google Fonts estava depois dos @tailwind.

**Solução Aplicada**: Movido o @import para o topo do arquivo index.css.

### 3. Lighthouse Error: "NO_FCP" (No First Contentful Paint)

**Problema**: Lighthouse não conseguia renderizar a página devido aos erros acima.

**Solução**: Corrigidos os erros de build, agora a página renderiza corretamente.

## Como Executar Lighthouse Agora

### Passo 1: Verificar que não há erros no console

1. Abra http://localhost:8090
2. Abra DevTools (F12)
3. Vá na aba Console
4. **Deve aparecer apenas**:
   - "Download the React DevTools..." (aviso normal)
   - Logs do Service Worker (normal)
5. **NÃO deve aparecer**:
   - Erros vermelhos
   - "can't detect preamble"
   - "@import must precede"

### Passo 2: Verificar que a página carrega

1. A página deve carregar completamente
2. Você deve ver o logo e o conteúdo
3. Navegação deve funcionar

### Passo 3: Executar Lighthouse

1. **Mantenha a aba em foco** (não minimize ou troque de aba)
2. Abra DevTools (F12)
3. Vá na aba "Lighthouse"
4. Selecione:
   - ✅ Performance
   - ✅ Accessibility
   - ✅ Best Practices
   - ✅ SEO
   - ✅ Progressive Web App
5. Modo: **Desktop** (primeiro)
6. Clique "Analyze page load"
7. **IMPORTANTE**: Mantenha a janela em foco durante todo o teste (30-60s)


## Dicas para Lighthouse Funcionar

### ✅ DO (Faça)

1. **Mantenha a janela em foco**
   - Não minimize
   - Não troque de aba
   - Não abra outros programas por cima

2. **Feche outras abas do Chrome**
   - Lighthouse precisa de recursos
   - Outras abas podem interferir

3. **Desabilite extensões (opcional)**
   - Extensões podem afetar os resultados
   - Use modo anônimo se necessário

4. **Aguarde o carregamento completo**
   - Espere a página carregar totalmente antes de iniciar
   - Verifique que não há spinners ou loading

5. **Use conexão estável**
   - Lighthouse testa performance de rede
   - Evite downloads ou streaming durante o teste

### ❌ DON'T (Não Faça)

1. **Não minimize a janela**
   - Lighthouse detecta e falha com "NO_FCP"

2. **Não troque de aba durante o teste**
   - O teste será invalidado

3. **Não interaja com a página durante o teste**
   - Deixe o Lighthouse controlar

4. **Não execute em modo privado com extensões**
   - Pode causar problemas de cache

## Erros Comuns do Lighthouse

### "Clearing the browser cache timed out"

**Causa**: Chrome não conseguiu limpar o cache a tempo.

**Solução**:
1. Feche e reabra o Chrome
2. Limpe o cache manualmente (Ctrl+Shift+Del)
3. Tente novamente

### "The page did not paint any content (NO_FCP)"

**Causa**: Página não renderizou ou janela não estava em foco.

**Soluções**:
1. ✅ Verifique que não há erros no console
2. ✅ Mantenha a janela em foco
3. ✅ Aguarde a página carregar completamente
4. ✅ Tente em modo anônimo

### "Lighthouse was unable to reliably load the page"

**Causa**: Problemas de rede ou timeout.

**Soluções**:
1. Verifique que http://localhost:8090 está acessível
2. Reinicie o Docker: `docker-compose restart`
3. Limpe o cache do navegador
4. Tente novamente


## Verificação Pré-Lighthouse

Execute este checklist antes de rodar o Lighthouse:

```bash
# 1. Verificar que a aplicação está rodando
curl -s http://localhost:8090 | grep -o "<title>.*</title>"
# Deve retornar: <title>Operação ALFA - Simulados Militares</title>

# 2. Verificar Service Worker
curl -s -o /dev/null -w "%{http_code}" http://localhost:8090/sw.js
# Deve retornar: 200

# 3. Verificar Manifest
curl -s http://localhost:8090/manifest.json | jq .name
# Deve retornar: "Alfa Quest - Simulados para Concursos"

# 4. Verificar logs do Vite (não deve ter erros)
docker logs simulados-vite --tail 20
```

### Checklist Visual

Abra http://localhost:8090 e verifique:

- [ ] Página carrega sem erros
- [ ] Logo aparece
- [ ] Navegação funciona
- [ ] Console não tem erros vermelhos
- [ ] Service Worker registrado (DevTools > Application > Service Workers)
- [ ] Manifest carrega (DevTools > Application > Manifest)

## Alternativa: Lighthouse CLI

Se o Lighthouse no DevTools continuar com problemas, use a CLI:

```bash
# Instalar Lighthouse globalmente
npm install -g lighthouse

# Executar audit
lighthouse http://localhost:8090 \
  --output html \
  --output-path ./lighthouse-report.html \
  --chrome-flags="--headless"

# Abrir relatório
open lighthouse-report.html
```

## Modo de Desenvolvimento vs Produção

### Desenvolvimento (Atual)
- Vite dev server rodando
- Hot Module Replacement ativo
- Source maps disponíveis
- **Performance pode ser menor**

### Produção (Recomendado para Lighthouse)
```bash
# Build de produção
docker exec simulados-vite npm run build --prefix /var/www/laravel

# Reiniciar servidor
docker-compose restart simulados-webserver

# Testar
curl http://localhost:8090
```

**Nota**: Para Lighthouse mais preciso, use build de produção.

## Resultados Esperados

Com os erros corrigidos, você deve ver:

### Desktop
- Performance: 85-95
- Accessibility: 90-100
- Best Practices: 85-95
- SEO: 90-100
- PWA: 90-100

### Mobile
- Performance: 70-85
- Accessibility: 90-100
- Best Practices: 85-95
- SEO: 90-100
- PWA: 90-100

## Próximos Passos

1. ✅ Erros corrigidos
2. ✅ Vite rodando sem erros
3. ✅ Página carregando corretamente
4. 🔄 Execute Lighthouse seguindo as dicas acima
5. 📝 Documente os resultados em LIGHTHOUSE_VALIDATION.md

## Suporte

Se ainda tiver problemas:

1. Verifique os logs: `docker-compose logs -f`
2. Reinicie tudo: `docker-compose restart`
3. Limpe o cache do navegador
4. Tente em modo anônimo
5. Use Lighthouse CLI como alternativa

