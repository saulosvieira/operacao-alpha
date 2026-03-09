# Requirements Document

## Introduction

Este documento especifica os requisitos para corrigir e dinamizar a tela /carreiras do sistema web de simulados. Atualmente, a página não exibe corretamente as informações de simulados vinculados às carreiras que existem no banco de dados. A versão em inglês (Careers.tsx) carrega dados da API mas não exibe a contagem de simulados, enquanto a versão em português (Carreiras.tsx) usa dados mockados em vez de dados reais do banco.

O objetivo é garantir que ambas as páginas exibam dados reais do banco de dados, incluindo contagem de simulados, editais, aprovados e outras informações relevantes para ajudar o usuário a escolher sua carreira.

## Glossary

- **Career_Page**: A página web acessível em /carreiras que exibe a lista de carreiras disponíveis
- **Career_API**: O endpoint da API REST que retorna dados de carreiras com contagens relacionadas
- **Career_Repository**: O repositório Laravel responsável por buscar dados de carreiras do banco de dados
- **Career_DTO**: O Data Transfer Object que estrutura os dados de carreira para a API
- **Frontend_Component**: Os componentes React (Careers.tsx e Carreiras.tsx) que renderizam a interface
- **Exam_Count**: A contagem de simulados disponíveis para uma carreira específica
- **Notice_Count**: A contagem de editais associados a uma carreira
- **Approved_Count**: A contagem de aprovados em uma carreira
- **Mock_Data**: Dados estáticos codificados no frontend (mockCarreiras) que devem ser removidos
- **Dynamic_Data**: Dados reais carregados do banco de dados via API

## Requirements

### Requirement 1: Exibir Contagem Real de Simulados

**User Story:** Como usuário do sistema, eu quero ver quantos simulados estão disponíveis para cada carreira, para que eu possa escolher uma carreira com conteúdo adequado às minhas necessidades.

#### Acceptance Criteria

1. WHEN a Career_Page é carregada, THE Frontend_Component SHALL fazer uma requisição à Career_API para obter dados de carreiras
2. WHEN a Career_API retorna dados, THE Frontend_Component SHALL exibir o Exam_Count para cada carreira
3. WHEN uma carreira não possui simulados, THE Frontend_Component SHALL exibir "0 simulados" ou mensagem equivalente
4. THE Career_API SHALL retornar o campo examsCount para cada carreira no response
5. THE Career_Repository SHALL utilizar withCount(['exams']) para calcular o Exam_Count corretamente

### Requirement 2: Remover Dados Mockados

**User Story:** Como desenvolvedor, eu quero que o sistema use apenas dados reais do banco de dados, para que as informações exibidas sejam sempre precisas e atualizadas.

#### Acceptance Criteria

1. THE Frontend_Component SHALL NOT utilizar Mock_Data (mockCarreiras) em nenhuma circunstância
2. WHEN a Career_Page é renderizada, THE Frontend_Component SHALL sempre buscar Dynamic_Data da Career_API
3. IF a Career_API falhar, THEN THE Frontend_Component SHALL exibir uma mensagem de erro apropriada
4. THE Frontend_Component SHALL remover todas as referências a Mock_Data do código

### Requirement 3: Exibir Informações Adicionais de Carreiras

**User Story:** Como usuário, eu quero ver informações completas sobre cada carreira (editais, aprovados, descrição), para que eu possa tomar uma decisão informada sobre qual carreira seguir.

#### Acceptance Criteria

1. WHEN a Career_Page exibe uma carreira, THE Frontend_Component SHALL mostrar o Notice_Count se disponível
2. WHEN a Career_Page exibe uma carreira, THE Frontend_Component SHALL mostrar o Approved_Count se disponível
3. WHEN a Career_Page exibe uma carreira, THE Frontend_Component SHALL mostrar a descrição da carreira se disponível
4. THE Career_API SHALL retornar campos noticesCount e approvedCount quando disponíveis
5. THE Career_Repository SHALL utilizar withCount(['notices', 'approved']) para calcular contagens relacionadas

### Requirement 4: Consistência entre Versões de Idioma

**User Story:** Como usuário, eu quero que as versões em português e inglês da página exibam as mesmas informações, para que a experiência seja consistente independente do idioma escolhido.

#### Acceptance Criteria

1. THE Frontend_Component em português (Carreiras.tsx) SHALL exibir os mesmos dados que a versão em inglês (Careers.tsx)
2. WHEN dados são carregados, THE Frontend_Component SHALL aplicar apenas traduções de texto, mantendo os dados idênticos
3. THE Career_API SHALL retornar os mesmos dados independente do idioma da requisição
4. WHEN uma carreira é exibida, THE Frontend_Component SHALL mostrar Exam_Count, Notice_Count e Approved_Count em ambos os idiomas

### Requirement 5: Interface Informativa e Útil

**User Story:** Como usuário, eu quero uma interface clara e organizada que me ajude a comparar carreiras facilmente, para que eu possa escolher a melhor opção para mim.

#### Acceptance Criteria

1. WHEN múltiplas carreiras são exibidas, THE Frontend_Component SHALL organizá-las de forma clara e escaneável
2. WHEN informações numéricas são exibidas, THE Frontend_Component SHALL formatá-las de forma legível (ex: "15 simulados", "3 editais")
3. THE Frontend_Component SHALL destacar visualmente carreiras com mais conteúdo disponível
4. WHEN o usuário visualiza uma carreira, THE Frontend_Component SHALL exibir todas as informações relevantes sem necessidade de navegação adicional
5. THE Frontend_Component SHALL manter responsividade em dispositivos móveis e desktop

### Requirement 6: Tratamento de Erros e Estados de Carregamento

**User Story:** Como usuário, eu quero feedback claro quando dados estão sendo carregados ou quando ocorrem erros, para que eu entenda o estado atual do sistema.

#### Acceptance Criteria

1. WHEN a Career_Page está carregando dados, THE Frontend_Component SHALL exibir um indicador de carregamento
2. IF a Career_API retorna erro, THEN THE Frontend_Component SHALL exibir uma mensagem de erro amigável
3. IF a Career_API retorna lista vazia, THEN THE Frontend_Component SHALL exibir mensagem informando que não há carreiras disponíveis
4. WHEN ocorre erro de rede, THE Frontend_Component SHALL permitir que o usuário tente novamente
5. THE Frontend_Component SHALL registrar erros no console para debugging sem expor detalhes técnicos ao usuário

### Requirement 7: Performance e Otimização

**User Story:** Como usuário, eu quero que a página carregue rapidamente, para que eu possa acessar as informações sem demora.

#### Acceptance Criteria

1. THE Career_API SHALL retornar dados de carreiras em menos de 500ms em condições normais
2. THE Career_Repository SHALL utilizar eager loading para evitar queries N+1
3. THE Frontend_Component SHALL cachear dados de carreiras por tempo razoável (ex: 5 minutos)
4. WHEN dados estão em cache, THE Frontend_Component SHALL exibi-los imediatamente
5. THE Career_API SHALL retornar apenas campos necessários para a listagem de carreiras

### Requirement 8: Integração com Backend Existente

**User Story:** Como desenvolvedor, eu quero utilizar a estrutura backend existente sem modificações desnecessárias, para que a implementação seja eficiente e mantenha a arquitetura atual.

#### Acceptance Criteria

1. THE Career_API SHALL utilizar o Career_Repository existente sem modificações na assinatura dos métodos
2. THE Career_DTO SHALL ser estendido apenas se necessário para incluir novos campos
3. WHEN o Career_Repository já retorna examsCount, THE Career_API SHALL utilizá-lo diretamente
4. THE Frontend_Component SHALL consumir a API REST existente seguindo os padrões estabelecidos
5. WHEN relacionamentos já existem no modelo Career, THE Career_Repository SHALL utilizá-los sem criar novos
