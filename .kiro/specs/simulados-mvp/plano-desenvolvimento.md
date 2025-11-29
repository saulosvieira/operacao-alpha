# Plano de Desenvolvimento - MVP Simulados

## Visão Geral do Projeto

Desenvolvimento de plataforma de simulados educacionais com:
- **Backend/Admin**: Laravel 12 + AdminLTE (já existente como base)
- **Frontend Usuário**: PWA responsiva integrada ao Laravel
- **Mobile**: WebView wrappers simples (Android/iOS) quando necessário
- **Infraestrutura**: Docker (MySQL 8.0, Redis, Nginx)

## Fase 0: Preparação e Limpeza (1-2 dias)

### 0.1 Ajustar Ambiente Docker
- [x] Renomear containers para evitar conflitos (simulados-*)
- [x] Ajustar portas (8090, 33090, 63790)
- [x] Remover serviço python-api não utilizado
- [ ] Atualizar .env do Laravel com novas credenciais
- [ ] Testar subida dos containers

### 0.2 Limpar Recursos Desnecessários
- [ ] Remover módulo Quotes (laravel/modules/Quotes/)
- [ ] Limpar rotas não utilizadas
- [ ] Remover controllers/models de exemplo
- [ ] Limpar views desnecessárias
- [ ] Manter apenas estrutura AdminLTE base

### 0.3 Configurar Base do Projeto
- [ ] Verificar versão do Laravel (deve ser 12.x)
- [ ] Configurar storage links
- [ ] Configurar filas com Redis
- [ ] Testar autenticação AdminLTE
- [ ] Configurar logs estruturados

**Entregável**: Ambiente limpo e funcional pronto para desenvolvimento

---

## Fase 1: Estrutura de Dados e Modelos (3-4 dias)

### 1.1 Migrations do Sistema de Simulados
```php
// Ordem de criação:
1. carreiras (id, nome, descricao, slug, ativa)
2. editais (id, carreira_id, titulo, descricao, data_publicacao, ativo)
3. simulados (id, carreira_id, titulo, descricao, tempo_limite_minutos, ativo)
4. questoes (id, simulado_id, numero_questao, enunciado, imagem_enunciado, 
             alternativa_a-e, imagem_a-e, resposta_correta, explicacao)
5. users (adicionar: subscription_status, subscription_expires_at, subscription_platform_id)
6. respostas_usuarios (id, user_id, simulado_id, questao_id, resposta_escolhida, correta, tempo_resposta_segundos)
7. resultados_simulados (id, user_id, simulado_id, pontuacao, total_questoes, acertos, tempo_total_segundos, finalizado_em)
8. rankings (id, user_id, pontuacao_diaria, pontuacao_semanal, data_calculo)
9. aprovados (id, carreira_id, edital_id, nome, posicao, ano)
```

### 1.2 Models e Relacionamentos
- [ ] Model Carreira (hasMany: editais, simulados)
- [ ] Model Edital (belongsTo: carreira)
- [ ] Model Simulado (belongsTo: carreira, hasMany: questoes)
- [ ] Model Questao (belongsTo: simulado)
- [ ] Model User (hasMany: respostas, resultados, ranking)
- [ ] Model RespostaUsuario (belongsTo: user, questao, simulado)
- [ ] Model ResultadoSimulado (belongsTo: user, simulado)
- [ ] Model Ranking (belongsTo: user)
- [ ] Model Aprovado (belongsTo: carreira, edital)

### 1.3 Seeders de Teste
- [ ] Seeder de carreiras (5-10 carreiras exemplo)
- [ ] Seeder de editais (2-3 por carreira)
- [ ] Seeder de usuário admin
- [ ] Seeder de usuários teste (assinantes e não-assinantes)

**Entregável**: Banco de dados estruturado com relacionamentos funcionais

---

## Fase 2: Painel Administrativo - CRUD Básico (4-5 dias)

### 2.1 Gestão de Carreiras
- [ ] Controller: CarreiraController (CRUD completo)
- [ ] Views: index, create, edit (AdminLTE)
- [ ] Validações: FormRequest
- [ ] Rota: /admin/carreiras

### 2.2 Gestão de Editais
- [ ] Controller: EditalController (CRUD completo)
- [ ] Views: index, create, edit
- [ ] Relacionamento com carreiras (select)
- [ ] Rota: /admin/editais

### 2.3 Gestão de Simulados
- [ ] Controller: SimuladoController (CRUD completo)
- [ ] Views: index, create, edit
- [ ] Campos: título, descrição, carreira, tempo limite
- [ ] Toggle ativo/inativo
- [ ] Rota: /admin/simulados

### 2.4 Gestão de Aprovados
- [ ] Controller: AprovadoController (CRUD completo)
- [ ] Views: index, create, edit
- [ ] Relacionamento com carreiras e editais
- [ ] Importação CSV simples (nome, carreira, edital, posição, ano)
- [ ] Rota: /admin/aprovados

**Entregável**: Painel admin funcional para gestão de entidades básicas

---

## Fase 3: Sistema de Questões e Importação (5-7 dias)

### 3.1 CRUD Manual de Questões
- [ ] Controller: QuestaoController
- [ ] View: formulário com 5 alternativas
- [ ] Upload de imagens (enunciado + alternativas)
- [ ] Editor WYSIWYG para enunciados (opcional)
- [ ] Preview de questão
- [ ] Associação com simulado
- [ ] Rota: /admin/questoes

### 3.2 Importação CSV/Excel de Questões
- [ ] Service: QuestaoImportService
- [ ] View: upload de CSV/Excel
- [ ] Validação de formato e estrutura
- [ ] Preview antes de importar
- [ ] Importação em lote com feedback
- [ ] Tratamento de erros linha a linha
- [ ] Template de exemplo para download

**Formato CSV/Excel esperado:**
```csv
numero_questao,simulado_id,enunciado,imagem_enunciado,alternativa_a,imagem_a,alternativa_b,imagem_b,alternativa_c,imagem_c,alternativa_d,imagem_d,alternativa_e,imagem_e,resposta_correta,explicacao
```

### 3.3 Sistema de Upload de Imagens
- [ ] Storage configurado (public/questoes/)
- [ ] Validação de tipos (jpg, png, gif, webp)
- [ ] Redimensionamento automático
- [ ] Otimização de imagens (spatie/image-optimizer)
- [ ] Nomenclatura padronizada
- [ ] Upload em lote
- [ ] Limpeza de imagens órfãs

### 3.4 Suporte a Excel (XLS/XLSX)
- [ ] Instalar maatwebsite/excel
- [ ] Adicionar suporte a importação Excel
- [ ] Validação de planilhas Excel
- [ ] Template Excel de exemplo
- [ ] Conversão automática Excel para CSV

**Entregável**: Sistema completo de gestão de questões com importação CSV/Excel e cadastro manual

---

## Fase 4: Frontend Usuário - Sistema de Simulados (7-10 dias)

### 4.1 Autenticação de Usuários
- [ ] Laravel Breeze ou Jetstream (simples)
- [ ] Registro de usuários
- [ ] Login/Logout
- [ ] Perfil básico
- [ ] Middleware de autenticação

### 4.2 Listagem e Seleção de Simulados
- [ ] Controller: SimuladoPublicoController
- [ ] View: listagem de simulados ativos
- [ ] Filtro por carreira
- [ ] Card com informações (título, tempo, nº questões)
- [ ] Botão "Iniciar Simulado"
- [ ] Rota: /simulados

### 4.3 Interface de Realização do Simulado
- [ ] View: tela de simulado
- [ ] Cronômetro regressivo (JavaScript)
- [ ] Navegação entre questões
- [ ] Marcação de respostas
- [ ] Salvamento automático (AJAX)
- [ ] Finalização manual ou automática
- [ ] Confirmação antes de finalizar
- [ ] Rota: /simulados/{id}/realizar

**Componentes JavaScript:**
```javascript
- CronometroSimulado.js (countdown timer)
- NavegacaoQuestoes.js (navegação e marcação)
- SalvamentoAutomatico.js (auto-save respostas)
- FinalizacaoSimulado.js (submit e confirmação)
```

### 4.4 Resultados e Histórico
- [ ] View: resultado imediato
- [ ] Exibição de pontuação
- [ ] Questões corretas/incorretas
- [ ] Gabarito com explicações
- [ ] View: histórico de simulados
- [ ] Listagem de tentativas anteriores
- [ ] Rota: /simulados/{id}/resultado
- [ ] Rota: /meu-historico

**Entregável**: Sistema funcional de simulados para usuários

---

## Fase 5: Sistema de Ranking (3-4 dias)

### 5.1 Cálculo de Pontuações
- [ ] Service: RankingService
- [ ] Lógica de pontuação por simulado
- [ ] Cálculo de pontuação diária
- [ ] Cálculo de pontuação semanal
- [ ] Job: AtualizarRankingJob (agendado)
- [ ] Command: php artisan ranking:atualizar

### 5.2 Interface de Ranking
- [ ] Controller: RankingController
- [ ] View: ranking global
- [ ] Tabs: Diário / Semanal
- [ ] Tabela com posição, nome, pontuação
- [ ] Destaque do usuário logado
- [ ] Paginação
- [ ] Rota: /ranking

**Entregável**: Sistema de ranking funcional e atualizado

---

## Fase 6: Sistema de Assinaturas (4-5 dias)

### 6.1 Controle de Acesso
- [ ] Middleware: CheckSubscription
- [ ] Enum: SubscriptionStatus (active, inactive, trial, expired)
- [ ] Lógica de verificação de assinatura
- [ ] Restrições para não-assinantes
- [ ] Mensagens de upgrade

### 6.2 Integração com Plataforma de Pagamento
- [ ] Controller: WebhookController
- [ ] Rota: /webhook/assinatura (POST, sem CSRF)
- [ ] Handler para Kiwify
- [ ] Handler para Hotmart (alternativo)
- [ ] Ativação automática de usuário
- [ ] Desativação automática
- [ ] Logs de webhooks recebidos

**Estrutura de Webhook:**
```php
// Kiwify exemplo
{
  "event": "subscription.created",
  "data": {
    "customer_email": "user@example.com",
    "status": "active",
    "expires_at": "2026-01-01"
  }
}
```

### 6.3 Painel de Assinaturas (Admin)
- [ ] View: listagem de assinantes
- [ ] Filtros: ativos, expirados, trial
- [ ] Ativação/desativação manual
- [ ] Histórico de webhooks
- [ ] Rota: /admin/assinaturas

**Entregável**: Sistema de assinaturas integrado e funcional

---

## Fase 7: PWA e Responsividade (3-4 dias)

### 7.1 Configuração PWA
- [ ] Instalar laravel-pwa ou configurar manualmente
- [ ] Criar manifest.json
- [ ] Configurar service worker
- [ ] Ícones em múltiplos tamanhos
- [ ] Splash screens
- [ ] Tema de cores

### 7.2 Otimização Responsiva
- [ ] CSS responsivo (mobile-first)
- [ ] Breakpoints: 320px, 768px, 1024px
- [ ] Testes em diferentes dispositivos
- [ ] Otimização de imagens
- [ ] Lazy loading

### 7.3 Funcionalidades Offline (Básicas)
- [ ] Cache de assets estáticos
- [ ] Cache de simulados iniciados
- [ ] Sincronização quando online
- [ ] Indicador de status de conexão

**Entregável**: PWA funcional e responsiva

---

## Fase 8: Testes e Ajustes (3-4 dias)

### 8.1 Testes Automatizados
- [ ] Feature tests: fluxo de simulado completo
- [ ] Feature tests: sistema de ranking
- [ ] Feature tests: webhooks
- [ ] Unit tests: cálculos de pontuação
- [ ] Unit tests: importação CSV

### 8.2 Testes Manuais
- [ ] Teste completo do fluxo de usuário
- [ ] Teste de responsividade
- [ ] Teste de performance
- [ ] Teste de webhooks com plataforma real
- [ ] Teste de importação CSV com dados reais

### 8.3 Correções e Refinamentos
- [ ] Correção de bugs identificados
- [ ] Ajustes de UX
- [ ] Otimizações de performance
- [ ] Melhorias de acessibilidade básica

**Entregável**: Sistema testado e refinado

---

## Fase 9: WebView Wrappers (Opcional - 2-3 dias)

### 9.1 Wrapper Android (Kotlin)
- [ ] Projeto Android Studio básico
- [ ] WebView carregando URL da aplicação
- [ ] Configurações de segurança
- [ ] Deep links básicos
- [ ] Splash screen
- [ ] Ícones e assets

### 9.2 Wrapper iOS (Swift)
- [ ] Projeto Xcode básico
- [ ] WKWebView carregando URL
- [ ] Configurações de privacidade
- [ ] Universal Links
- [ ] Splash screen
- [ ] Ícones e assets

**Nota**: Esta fase só será executada se houver necessidade de publicação nas lojas. O PWA pode ser suficiente inicialmente.

**Entregável**: Apps wrapper prontos para submissão

---

## Fase 10: Deploy e Documentação (2-3 dias)

### 10.1 Preparação para Produção
- [ ] Configurar .env de produção
- [ ] Otimizar autoload do Composer
- [ ] Compilar assets (npm run build)
- [ ] Configurar cache de rotas e config
- [ ] Configurar queue workers
- [ ] Configurar cron jobs

### 10.2 Deploy
- [ ] Configurar servidor (fornecido pelo cliente)
- [ ] Configurar banco de dados
- [ ] Migrar banco de dados
- [ ] Configurar SSL/HTTPS
- [ ] Configurar backups automáticos
- [ ] Testar em produção

### 10.3 Documentação
- [ ] Guia de operação do painel admin
- [ ] Documentação de importação CSV
- [ ] Documentação de webhooks
- [ ] Guia de troubleshooting
- [ ] README atualizado

**Entregável**: Sistema em produção com documentação

---

## Cronograma Estimado

| Fase | Descrição | Duração | Prazo |
|------|-----------|---------|-------|
| 0 | Preparação e Limpeza | 1-2 dias | Semana 1 |
| 1 | Estrutura de Dados | 3-4 dias | Semana 1-2 |
| 2 | Painel Admin CRUD | 4-5 dias | Semana 2-3 |
| 3 | Sistema de Questões | 5-7 dias | Semana 3-4 |
| 4 | Frontend Simulados | 7-10 dias | Semana 4-6 |
| 5 | Sistema de Ranking | 3-4 dias | Semana 6-7 |
| 6 | Sistema de Assinaturas | 4-5 dias | Semana 7-8 |
| 7 | PWA e Responsividade | 3-4 dias | Semana 8-9 |
| 8 | Testes e Ajustes | 3-4 dias | Semana 9-10 |
| 9 | WebView Wrappers (Opcional) | 2-3 dias | Semana 10-11 |
| 10 | Deploy e Documentação | 2-3 dias | Semana 11-12 |

**Total estimado**: 37-51 dias úteis (aproximadamente 2-2.5 meses)

---

## Dependências e Pré-requisitos

### Do Cliente (CONTRATANTE)
- [ ] Acesso ao servidor/hosting
- [ ] Credenciais da plataforma de assinaturas (Kiwify/Hotmart)
- [ ] CSV de amostra com questões (10-20 linhas)
- [ ] Contas Google Play e App Store (se necessário)
- [ ] Materiais de branding (logo, ícones, cores)
- [ ] Política de privacidade e termos de uso

### Técnicas
- [ ] Docker e Docker Compose instalados
- [ ] PHP 8.2+
- [ ] Composer
- [ ] Node.js e NPM
- [ ] MySQL 8.0
- [ ] Redis

---

## Marcos de Entrega e Pagamento

Conforme contrato:

1. **20% na assinatura** - Início do projeto
2. **20% em 10/out/2025** - Fases 0-2 concluídas (Admin CRUD)
3. **20% em 10/nov/2025** - Fases 3-4 concluídas (Questões + Simulados)
4. **20% em 10/dez/2025** - Fases 5-6 concluídas (Ranking + Assinaturas)
5. **20% em 10/jan/2026** - Fases 7-10 concluídas (PWA + Deploy)

---

## Riscos e Mitigações

| Risco | Probabilidade | Impacto | Mitigação |
|-------|---------------|---------|-----------|
| Atraso na aprovação App Store | Média | Alto | Focar em PWA primeiro, apps wrapper como secundário |
| Mudanças na API de pagamento | Baixa | Médio | Abstrair integração em Service dedicado |
| Performance com muitas questões | Média | Médio | Implementar paginação e cache desde o início |
| Complexidade de extração de PDF | Alta | Baixo | Deixar como opcional, priorizar importação CSV manual |
| Indisponibilidade de credenciais | Média | Alto | Solicitar com antecedência, ter plano B |

---

## Próximos Passos Imediatos

1. ✅ Atualizar docker-compose.yml com nomes únicos
2. ⏳ Subir containers e testar ambiente
3. ⏳ Atualizar .env com novas credenciais
4. ⏳ Limpar módulo Quotes e recursos não utilizados
5. ⏳ Criar primeira migration (carreiras)
6. ⏳ Iniciar desenvolvimento do CRUD de carreiras

---

## Notas Importantes

- **Foco em MVP**: Implementar apenas o essencial, sem over-engineering
- **PWA First**: Priorizar PWA funcional antes de apps nativos
- **Importação CSV**: Priorizar importação manual antes de processamento automático de PDF
- **Testes contínuos**: Testar cada funcionalidade antes de avançar
- **Comunicação**: Manter cliente informado sobre progresso semanalmente
