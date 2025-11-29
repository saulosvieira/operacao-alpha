# Resumo Executivo - MVP Simulados

## 📋 Visão Geral

Plataforma de simulados educacionais com painel administrativo Laravel + AdminLTE e frontend PWA responsivo para usuários. Foco em MVP funcional com possibilidade de wrappers WebView para Android/iOS.

## 🎯 Objetivos Principais

1. **Painel Admin**: Gestão completa de simulados, questões, carreiras, editais e aprovados
2. **Sistema de Simulados**: Cronômetro, questões com imagens, resultados imediatos
3. **Ranking Global**: Placar diário e semanal
4. **Assinaturas**: Integração com Kiwify/Hotmart via webhook
5. **PWA**: Aplicação web progressiva responsiva

## 🏗️ Arquitetura

- **Backend**: Laravel 12 + AdminLTE
- **Frontend**: Blade + JavaScript (PWA)
- **Database**: MySQL 8.0
- **Cache/Queue**: Redis
- **Containers**: Docker (nomes únicos: simulados-*)
- **Portas**: 8090 (web), 33090 (mysql), 63790 (redis)

## 📦 Entregas por Fase

### Fase 0: Preparação (1-2 dias)
- ✅ Docker configurado com nomes únicos
- ⏳ Limpeza de módulos não utilizados (Quotes)
- ⏳ Ambiente testado e funcional

### Fase 1: Estrutura de Dados (3-4 dias)
- 9 migrations principais
- 9 models com relacionamentos
- Seeders de teste

### Fase 2: Painel Admin CRUD (4-5 dias)
- Carreiras, Editais, Simulados, Aprovados
- Interface AdminLTE completa

### Fase 3: Sistema de Questões (5-7 dias)
- CRUD de questões com imagens
- Importação CSV/Excel
- Upload em lote de imagens
- Template de exemplo

### Fase 4: Frontend Simulados (7-10 dias)
- Autenticação de usuários
- Listagem de simulados
- Interface de realização (cronômetro, navegação)
- Resultados e histórico

### Fase 5: Ranking (3-4 dias)
- Cálculo de pontuações
- Interface de ranking global

### Fase 6: Assinaturas (4-5 dias)
- Middleware de controle de acesso
- Webhook handler (Kiwify/Hotmart)
- Painel de gestão de assinantes

### Fase 7: PWA (3-4 dias)
- Manifest e service worker
- Responsividade mobile-first
- Funcionalidades offline básicas

### Fase 8: Testes (3-4 dias)
- Testes automatizados
- Testes manuais
- Correções e refinamentos

### Fase 9: WebView Wrappers (2-3 dias - OPCIONAL)
- App Android (Kotlin)
- App iOS (Swift)

### Fase 10: Deploy (2-3 dias)
- Configuração de produção
- Deploy no servidor do cliente
- Documentação

## 📅 Cronograma

**Total**: 37-51 dias úteis (2-2.5 meses)
**Prazo contratual**: Até janeiro/2026

## 💰 Marcos de Pagamento

1. **20%** - Assinatura (início)
2. **20%** - 10/out/2025 (Fases 0-2: Admin CRUD)
3. **20%** - 10/nov/2025 (Fases 3-4: Questões + Simulados)
4. **20%** - 10/dez/2025 (Fases 5-6: Ranking + Assinaturas)
5. **20%** - 10/jan/2026 (Fases 7-10: PWA + Deploy)

## 🔑 Dependências do Cliente

- [ ] Acesso ao servidor/hosting
- [ ] Credenciais plataforma de assinaturas
- [ ] CSV de amostra com questões
- [ ] Contas Google Play/App Store (se necessário)
- [ ] Materiais de branding
- [ ] Política de privacidade e termos

## 🎨 Estrutura de Dados Principal

```
Carreira
  └── Simulado (tempo_limite, ativo)
       └── Questao (enunciado, 5 alternativas, imagens, resposta_correta)
            └── RespostaUsuario (user, resposta_escolhida, correta)

User (subscription_status, subscription_expires_at)
  ├── ResultadoSimulado (pontuacao, tempo_total)
  └── Ranking (pontuacao_diaria, pontuacao_semanal)
```

## 📝 Formato CSV de Importação

```csv
numero_questao,simulado_id,enunciado,imagem_enunciado,
alternativa_a,imagem_a,alternativa_b,imagem_b,
alternativa_c,imagem_c,alternativa_d,imagem_d,
alternativa_e,imagem_e,resposta_correta,explicacao
```

## 🚀 Próximos Passos Imediatos

1. ✅ Atualizar docker-compose.yml
2. ⏳ Subir containers e testar
3. ⏳ Atualizar .env do Laravel
4. ⏳ Limpar módulo Quotes
5. ⏳ Criar primeira migration (carreiras)
6. ⏳ Desenvolver CRUD de carreiras

## 📚 Documentos de Referência

- `plano-desenvolvimento.md` - Plano detalhado com todas as fases
- `checklist-limpeza.md` - Checklist de limpeza do projeto base
- `env-config.md` - Configurações do .env
- `requirements.md` - Requisitos funcionais completos
- `design.md` - Arquitetura e design técnico
- `tasks.md` - Lista de tarefas detalhadas

## ⚠️ Decisões Importantes

1. **PWA First**: Priorizar PWA funcional antes de apps nativos
2. **CSV/Excel Manual**: Importação manual de CSV/Excel (SEM processamento de PDF)
3. **Cadastro Manual**: Formulário completo para cadastro individual de questões
4. **MVP Focado**: Implementar apenas o essencial, sem over-engineering
5. **Containers Únicos**: Nomes e portas únicos para evitar conflitos
6. **Redis para Tudo**: Cache, sessões e filas no Redis

## 🎯 Critérios de Sucesso

- [ ] Painel admin funcional para gestão completa
- [ ] Usuários conseguem realizar simulados cronometrados
- [ ] Resultados imediatos e histórico funcionando
- [ ] Ranking global atualizado
- [ ] Assinaturas ativadas/desativadas automaticamente via webhook
- [ ] PWA instalável e responsivo
- [ ] Sistema testado e em produção

---

**Status Atual**: Fase 0 - Preparação iniciada
**Última Atualização**: 18/11/2025
