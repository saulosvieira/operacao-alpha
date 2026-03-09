# Requirements Document

## Introduction

Este documento especifica os requisitos para corrigir a funcionalidade da tela /carreiras no sistema de simulados. A tela atualmente não exibe a contagem de simulados vinculados às carreiras, apesar do backend já fornecer essa informação através do campo `exams_count`. O objetivo é sincronizar o frontend com os dados reais do banco de dados e remover dependências de dados mockados.

## Glossary

- **Career**: Carreira profissional (ex: Polícia Federal, Receita Federal) que agrupa simulados relacionados
- **Exam**: Simulado/prova vinculado a uma carreira específica
- **Frontend**: Aplicação React + TypeScript que renderiza a interface do usuário
- **Backend**: API Laravel que fornece os dados através de endpoints REST
- **CareerData**: DTO (Data Transfer Object) que representa uma carreira com seus dados
- **API_Response**: Resposta da API no formato `{ data: Career[] }`
- **exams_count**: Campo numérico que indica a quantidade de simulados ativos vinculados a uma carreira
- **Active_Career**: Carreira com status `active = true`
- **Active_Exam**: Simulado com status `active = true`

## Requirements

### Requirement 1: Exibir Contagem de Simulados

**User Story:** Como usuário, eu quero ver quantos simulados estão disponíveis para cada carreira, para que eu possa escolher carreiras com mais conteúdo disponível.

#### Acceptance Criteria

1. WHEN a API retorna carreiras com o campo `exams_count`, THEN o Frontend SHALL exibir esse valor na interface
2. WHEN uma carreira tem 0 simulados, THEN o Sistema SHALL exibir "0 simulados disponíveis"
3. WHEN uma carreira tem 1 simulado, THEN o Sistema SHALL exibir "1 simulado disponível" (singular)
4. WHEN uma carreira tem múltiplos simulados, THEN o Sistema SHALL exibir "N simulados disponíveis" (plural)
5. THE Frontend SHALL utilizar o campo `exams_count` retornado pela API sem transformações adicionais

### Requirement 2: Sincronizar Interface TypeScript com Backend

**User Story:** Como desenvolvedor, eu quero que as interfaces TypeScript reflitam os dados reais do backend, para que não haja inconsistências entre frontend e backend.

#### Acceptance Criteria

1. THE Interface `Career` no arquivo types/index.ts SHALL incluir o campo `exams_count` do tipo `number`
2. THE Interface `Career` no arquivo services/careers.ts SHALL incluir o campo `exams_count` do tipo `number`
3. WHEN o Backend adiciona novos campos ao CareerData, THEN as interfaces TypeScript SHALL ser atualizáveis sem quebrar código existente
4. THE Sistema SHALL manter compatibilidade com o campo legado `totalExams` durante período de transição

### Requirement 3: Busca e Filtragem de Carreiras

**User Story:** Como usuário, eu quero buscar carreiras por nome ou descrição, para que eu possa encontrar rapidamente a carreira desejada.

#### Acceptance Criteria

1. WHEN o usuário digita no campo de busca, THEN o Sistema SHALL filtrar carreiras em tempo real
2. THE Sistema SHALL buscar tanto no campo `name` quanto no campo `description` da carreira
3. THE Busca SHALL ser case-insensitive (não diferenciar maiúsculas de minúsculas)
4. WHEN nenhuma carreira corresponde à busca, THEN o Sistema SHALL exibir mensagem "Nenhuma carreira encontrada"
5. WHEN o campo de busca está vazio, THEN o Sistema SHALL exibir todas as carreiras disponíveis

### Requirement 4: Navegação para Simulados da Carreira

**User Story:** Como usuário, eu quero clicar em uma carreira e ver seus simulados, para que eu possa iniciar um simulado específico.

#### Acceptance Criteria

1. WHEN o usuário clica em uma carreira, THEN o Sistema SHALL navegar para `/carreiras/{id}/simulados`
2. THE Sistema SHALL utilizar o `id` numérico da carreira na URL
3. WHEN a navegação ocorre, THEN o Sistema SHALL preservar o contexto da carreira selecionada

### Requirement 5: Estados de Loading e Erro

**User Story:** Como usuário, eu quero feedback visual durante o carregamento e em caso de erros, para que eu saiba o status da operação.

#### Acceptance Criteria

1. WHEN a página inicia o carregamento, THEN o Sistema SHALL exibir um indicador de loading
2. WHEN os dados são carregados com sucesso, THEN o Sistema SHALL ocultar o indicador de loading
3. IF ocorrer erro na requisição, THEN o Sistema SHALL exibir mensagem de erro descritiva
4. WHEN há erro de rede, THEN o Sistema SHALL exibir "Erro ao carregar carreiras"
5. WHEN a API retorna erro específico, THEN o Sistema SHALL exibir a mensagem de erro da API

### Requirement 6: Exibir Apenas Carreiras Ativas

**User Story:** Como usuário, eu quero ver apenas carreiras ativas, para que eu não veja carreiras descontinuadas ou em manutenção.

#### Acceptance Criteria

1. THE Backend SHALL retornar apenas carreiras com `active = true`
2. THE Backend SHALL contar apenas simulados com `active = true` no campo `exams_count`
3. WHEN uma carreira é desativada, THEN ela SHALL não aparecer na listagem do frontend
4. THE Sistema SHALL ordenar carreiras alfabeticamente por nome

### Requirement 7: Remover Dependências de Dados Mockados

**User Story:** Como desenvolvedor, eu quero remover todos os dados mockados do código, para que o sistema utilize apenas dados reais do banco de dados.

#### Acceptance Criteria

1. THE Sistema SHALL não conter referências a `mockCarreiras` ou dados hardcoded
2. THE Sistema SHALL buscar 100% dos dados através da API `/api/careers`
3. WHEN não há carreiras no banco de dados, THEN o Sistema SHALL exibir mensagem apropriada
4. THE Sistema SHALL funcionar corretamente mesmo com banco de dados vazio

### Requirement 8: Responsividade e Acessibilidade

**User Story:** Como usuário, eu quero que a interface seja responsiva e acessível, para que eu possa usar em diferentes dispositivos.

#### Acceptance Criteria

1. THE Interface SHALL ser responsiva em dispositivos mobile, tablet e desktop
2. THE Sistema SHALL utilizar componentes acessíveis (ARIA labels quando necessário)
3. WHEN o usuário navega por teclado, THEN todos os elementos interativos SHALL ser acessíveis
4. THE Sistema SHALL manter contraste adequado entre texto e fundo
5. THE Sistema SHALL utilizar ícones com significado semântico claro
