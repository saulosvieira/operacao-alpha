# Documento de Design - MVP Simulados

## Visão Geral

O sistema será desenvolvido como uma aplicação web responsiva em Laravel 12 + AdminLTE, encapsulada em aplicativos móveis através de WebView. A arquitetura seguirá o padrão MVC do Laravel com integração webhook para plataformas de pagamento.

## Arquitetura

### Arquitetura Geral
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Web Browser   │    │   PWA Mobile    │    │  WebView Wrap   │
│   (Desktop)     │    │   (Installed)   │    │  (Android/iOS)  │
└─────────┬───────┘    └─────────┬───────┘    └─────────┬───────┘
          │                      │                      │
          └──────────────────────┼──────────────────────┘
                                 │
                    ┌─────────────▼─────────────┐
                    │   Laravel 12 PWA App     │
                    │  (AdminLTE + Frontend)   │
                    │   Service Worker + Cache │
                    └─────────────┬─────────────┘
                                 │
                    ┌─────────────▼─────────────┐
                    │    MySQL 8.0 Database    │
                    │    Redis Cache/Queue     │
                    └───────────────────────────┘
```

### Integração com Plataforma de Pagamento
```
┌─────────────────┐    webhook    ┌─────────────────┐
│   Kiwify/       │──────────────▶│   Laravel App   │
│   Hotmart/etc   │               │   (Webhook      │
│                 │               │    Handler)     │
└─────────────────┘               └─────────────────┘
```

## Componentes e Interfaces

### 1. Aplicativos Móveis (WebView Wrappers)

**Android (Kotlin/Java)**
- WebView principal carregando a aplicação Laravel
- Deep links para compartilhamento
- Configurações de segurança para WebView
- Splash screen com branding

**iOS (Swift)**
- WKWebView carregando a aplicação Laravel
- Funcionalidades nativas mínimas para aprovação na App Store
- Universal Links para deep linking
- Configurações de privacidade

### 2. Aplicação Web Laravel

**Controllers Principais:**
- `SimuladoController` - Gestão de simulados
- `QuestaoController` - Gestão de questões
- `RankingController` - Sistema de ranking
- `AssinaturaController` - Gestão de assinaturas
- `WebhookController` - Processamento de webhooks
- `AdminController` - Painel administrativo

**Models Principais:**
- `User` - Usuários do sistema
- `Simulado` - Simulados disponíveis
- `Questao` - Banco de questões
- `Resposta` - Respostas dos usuários
- `Ranking` - Pontuações e rankings
- `Assinatura` - Status de assinaturas
- `Carreira` - Carreiras disponíveis
- `Edital` - Editais de concursos

### 3. Sistema de Importação de Questões

**Componente de Importação:**
```php
class QuestaoImportService
{
    public function importarCSV(string $caminhoArquivo, int $simuladoId): array
    public function importarExcel(string $caminhoArquivo, int $simuladoId): array
    public function validarArquivo(UploadedFile $arquivo): bool
}
```

**Fluxo de Importação:**
1. Upload do arquivo CSV/XLS pelo administrador
2. Validação do formato e estrutura
3. Preview das questões antes de importar
4. Importação em lote para o banco de dados
5. Relatório de sucesso/erros

## Modelos de Dados

### Estrutura do Banco de Dados

```sql
-- Usuários
users (
    id, name, email, password, subscription_status, 
    subscription_expires_at, created_at, updated_at
)

-- Simulados
simulados (
    id, titulo, descricao, tempo_limite, ativo, 
    carreira_id, created_at, updated_at
)

-- Questões
questoes (
    id, numero_questao, simulado_id, enunciado, imagem_enunciado,
    alternativa_a, imagem_a,
    alternativa_b, imagem_b,
    alternativa_c, imagem_c,
    alternativa_d, imagem_d,
    alternativa_e, imagem_e,
    resposta_correta, explicacao,
    created_at, updated_at
)

-- Respostas dos usuários
respostas_usuarios (
    id, user_id, simulado_id, questao_id, 
    resposta_escolhida, correta, tempo_resposta,
    created_at, updated_at
)

-- Resultados de simulados
resultados_simulados (
    id, user_id, simulado_id, pontuacao, 
    tempo_total, finalizado_em, created_at, updated_at
)

-- Rankings
rankings (
    id, user_id, pontuacao_diaria, pontuacao_semanal,
    data_calculo, created_at, updated_at
)

-- Carreiras
carreiras (
    id, nome, descricao, ativa, created_at, updated_at
)

-- Editais
editais (
    id, titulo, descricao, carreira_id, data_publicacao,
    ativo, created_at, updated_at
)

-- Aprovados
aprovados (
    id, nome, carreira_id, edital_id, posicao,
    ano, created_at, updated_at
)
```

### Estrutura CSV para Importação de Questões

Com base no modelo de prova objetiva padrão, o CSV deve seguir este formato:

```csv
numero_questao,simulado_id,enunciado,imagem_enunciado,alternativa_a,imagem_a,alternativa_b,imagem_b,alternativa_c,imagem_c,alternativa_d,imagem_d,alternativa_e,imagem_e,resposta_correta,explicacao
1,1,"Qual é a capital do Brasil?","","São Paulo","","Rio de Janeiro","","Brasília","","Salvador","","Belo Horizonte","","C","Brasília é a capital federal do Brasil desde 1960."
2,1,"Observe a imagem e identifique o elemento:","questao_002_enunciado.jpg","Elemento A","questao_002_a.jpg","Elemento B","questao_002_b.jpg","Elemento C","questao_002_c.jpg","Elemento D","questao_002_d.jpg","Elemento E","questao_002_e.jpg","A","A resposta correta é A porque..."
```

**Campos do CSV:**
- `numero_questao`: Número sequencial da questão (1, 2, 3, etc.)
- `simulado_id`: ID do simulado ao qual a questão pertence
- `enunciado`: Texto do enunciado da questão
- `imagem_enunciado`: Nome do arquivo de imagem do enunciado (opcional, vazio se não houver)
- `alternativa_a` até `alternativa_e`: Texto de cada alternativa
- `imagem_a` até `imagem_e`: Nome do arquivo de imagem de cada alternativa (opcional, vazio se não houver)
- `resposta_correta`: Letra da resposta correta (A, B, C, D ou E)
- `explicacao`: Explicação da resposta (opcional)

**Observações importantes:**
- Imagens devem ser extraídas e salvas separadamente com nomenclatura padronizada
- O sistema deve suportar questões com ou sem imagens
- Cada alternativa pode ter texto, imagem ou ambos
- O enunciado pode conter imagens adicionais ao texto

## Tratamento de Erros

### Estratégias de Error Handling

**Aplicação Web:**
- Try-catch em operações críticas
- Logs estruturados com Monolog
- Páginas de erro personalizadas
- Validação de entrada robusta

**WebView Apps:**
- Fallback para conexão offline
- Tratamento de erros de rede
- Cache local para funcionalidades básicas

**Integração Webhook:**
- Retry automático para falhas temporárias
- Queue system para processamento assíncrono
- Logs detalhados de transações

## Estratégia de Testes

### Testes Automatizados

**Unit Tests:**
- Models e suas relações
- Lógica de negócio em Services
- Validações e transformações

**Feature Tests:**
- Fluxos completos de simulados
- Sistema de ranking
- Processamento de webhooks
- Importação de CSV

**Browser Tests (Laravel Dusk):**
- Fluxo completo do usuário
- Responsividade em diferentes dispositivos
- Integração WebView

### Testes Manuais

**Aplicativos Móveis:**
- Testes em dispositivos reais Android/iOS
- Validação de performance do WebView
- Testes de deep links

**Integração:**
- Testes de webhook com plataformas reais
- Validação de importação CSV
- Testes de carga no sistema de ranking

## Ferramentas e Tecnologias para Importação

### Bibliotecas Recomendadas

**Para Importação de Arquivos:**
- `maatwebsite/excel` (PHP) - Importação de CSV e Excel (XLS/XLSX)
- `league/csv` (PHP) - Manipulação avançada de CSV

**Para Processamento de Imagens:**
- `intervention/image` (PHP) - Manipulação e redimensionamento de imagens
- `spatie/image-optimizer` (PHP) - Otimização de imagens para web

### Fluxo de Importação de Questões

1. **Upload e Validação**
   - Validar formato do arquivo (CSV ou XLS/XLSX)
   - Verificar tamanho do arquivo
   - Validar estrutura das colunas

2. **Processamento do Arquivo**
   - Ler todas as linhas do arquivo
   - Validar dados de cada questão
   - Verificar campos obrigatórios
   - Validar formato das respostas (A, B, C, D, E)

3. **Preview e Confirmação**
   - Mostrar preview das questões a serem importadas
   - Exibir erros de validação por linha
   - Permitir correção antes da importação final

4. **Importação Final**
   - Salvar questões no banco de dados
   - Processar upload de imagens referenciadas
   - Gerar relatório de importação
   - Exibir estatísticas (sucessos/erros)

### Interface de Importação no Painel Admin

O painel administrativo deve fornecer uma interface para:

1. **Upload de Arquivo CSV/XLS**
   - Seleção do arquivo CSV ou Excel
   - Seleção do simulado ao qual as questões pertencem
   - Template de exemplo para download

2. **Validação e Preview**
   - Mostrar preview das questões antes de importar
   - Exibir erros de validação por linha
   - Permitir correção de dados inválidos
   - Validar se todas as questões têm 5 alternativas

3. **Upload de Imagens**
   - Upload em lote de imagens das questões
   - Nomenclatura padronizada: `questao_{numero}_{tipo}.{ext}`
   - Tipos: `enunciado`, `a`, `b`, `c`, `d`, `e`
   - Otimização automática de imagens para web
   - Preview de imagens antes de salvar

4. **Cadastro Manual**
   - Formulário completo para cadastro individual
   - Editor WYSIWYG para enunciados
   - Upload de imagens por questão
   - Preview da questão antes de salvar

5. **Relatório de Importação**
   - Número de questões importadas com sucesso
   - Lista de erros encontrados
   - Opção de exportar relatório
   - Estatísticas da importação