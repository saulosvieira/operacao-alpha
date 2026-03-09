# Implementation Plan: Careers Page Fix

## Overview

Este plano implementa as correções necessárias para sincronizar a tela /carreiras com os dados reais do backend. O foco está em atualizar as interfaces TypeScript para incluir o campo `exams_count` e modificar o componente React para exibir a contagem de simulados. Não há mudanças no backend, que já está correto e funcional.

## Tasks

- [x] 1. Atualizar interfaces TypeScript para incluir exams_count
  - [x] 1.1 Adicionar campo exams_count à interface Career em types/index.ts
    - Adicionar `exams_count: number` à interface Career
    - Manter compatibilidade com código existente
    - _Requirements: 2.1_
  
  - [x] 1.2 Atualizar interface Career em services/careers.ts
    - Adicionar `exams_count: number` à interface Career
    - Marcar `totalExams` como opcional e deprecated
    - Adicionar comentário JSDoc indicando deprecation
    - _Requirements: 2.2, 2.4_

- [x] 2. Implementar exibição de contagem de simulados no componente Careers.tsx
  - [x] 2.1 Criar função helper para formatar texto de contagem
    - Implementar `formatSimuladosText(count: number): string`
    - Retornar singular "simulado" quando count === 1
    - Retornar plural "simulados" quando count !== 1
    - Adicionar "(em breve)" quando count === 0
    - _Requirements: 1.2, 1.3, 1.4_
  
  - [ ]* 2.2 Escrever testes unitários para formatSimuladosText
    - Testar caso count = 0 → "0 simulados (em breve)"
    - Testar caso count = 1 → "1 simulado disponível"
    - Testar caso count > 1 → "N simulados disponíveis"
    - _Requirements: 1.2, 1.3, 1.4_
  
  - [ ]* 2.3 Escrever property test para gramática singular/plural
    - **Property 3: Singular/Plural Grammar**
    - **Validates: Requirements 1.3, 1.4**
    - Gerar números aleatórios e verificar gramática correta
    - Configurar para 100 iterações mínimas
  
  - [x] 2.4 Atualizar JSX do componente Careers.tsx para exibir contagem
    - Substituir texto estático "Simulados disponíveis" por chamada a formatSimuladosText
    - Usar `career.exams_count` como parâmetro
    - Manter ícone Users e estrutura visual existente
    - _Requirements: 1.1, 1.5_
  
  - [ ]* 2.5 Escrever property test para precisão da contagem exibida
    - **Property 2: Count Display Accuracy**
    - **Validates: Requirements 1.1, 1.5**
    - Gerar carreiras aleatórias e verificar que valor exibido = exams_count
    - Configurar para 100 iterações mínimas

- [x] 3. Checkpoint - Verificar exibição de contagem
  - Executar aplicação e verificar que contagens são exibidas corretamente
  - Testar com carreiras que têm 0, 1 e múltiplos simulados
  - Perguntar ao usuário se há dúvidas ou problemas

- [ ] 4. Implementar melhorias no filtro de busca
  - [x] 4.1 Extrair lógica de filtro para função testável
    - Criar `filterCareers(careers: Career[], searchTerm: string): Career[]`
    - Implementar busca case-insensitive em name e description
    - Retornar todas as carreiras quando searchTerm está vazio
    - _Requirements: 3.1, 3.2, 3.3, 3.5_
  
  - [ ]* 4.2 Escrever testes unitários para casos específicos de busca
    - Testar busca vazia retorna todas as carreiras
    - Testar busca por nome exato
    - Testar busca por descrição parcial
    - Testar busca case-insensitive
    - Testar busca sem resultados
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_
  
  - [ ]* 4.3 Escrever property test para consistência do filtro
    - **Property 4: Search Filter Consistency**
    - **Validates: Requirements 3.2, 3.3**
    - Gerar carreiras e termos de busca aleatórios
    - Verificar que todos os resultados contêm o termo em name ou description
    - Configurar para 100 iterações mínimas
  
  - [x] 4.4 Atualizar componente para usar função filterCareers
    - Substituir lógica inline por chamada a filterCareers
    - Manter comportamento existente
    - _Requirements: 3.1_

- [x] 5. Implementar verificação de ordenação alfabética
  - [x] 5.1 Verificar se dados do backend já vêm ordenados
    - Analisar resposta da API /api/careers
    - Confirmar que CareerRepository.getAllActive() usa orderBy('name')
    - _Requirements: 6.4_
  
  - [ ]* 5.2 Escrever property test para ordenação alfabética
    - **Property 9: Alphabetical Ordering**
    - **Validates: Requirements 6.4**
    - Gerar listas aleatórias de carreiras
    - Verificar que lista exibida está em ordem alfabética
    - Configurar para 100 iterações mínimas
  
  - [x] 5.3 Adicionar ordenação no frontend se necessário
    - Se backend não ordenar, adicionar sort no frontend
    - Usar localeCompare para ordenação correta de caracteres especiais
    - _Requirements: 6.4_

- [x] 6. Melhorar tratamento de erros e estados vazios
  - [x] 6.1 Refatorar lógica de mensagens de erro
    - Criar função `getErrorMessage(error: any): string`
    - Priorizar mensagem da API quando disponível
    - Retornar mensagem padrão como fallback
    - _Requirements: 5.3, 5.4, 5.5_
  
  - [ ]* 6.2 Escrever testes unitários para mensagens de erro
    - Testar erro com mensagem da API
    - Testar erro sem mensagem (network error)
    - Testar diferentes formatos de erro
    - _Requirements: 5.3, 5.4, 5.5_
  
  - [ ]* 6.3 Escrever property test para exibição de erros
    - **Property 8: Error Message Display**
    - **Validates: Requirements 5.3, 5.5**
    - Gerar erros aleatórios com e sem mensagens
    - Verificar priorização correta de mensagens
    - Configurar para 100 iterações mínimas
  
  - [x] 6.4 Atualizar componente para usar getErrorMessage
    - Substituir lógica inline por chamada a getErrorMessage
    - Manter comportamento existente
    - _Requirements: 5.3_
  
  - [x] 6.5 Melhorar mensagens de estado vazio
    - Atualizar mensagem quando não há carreiras no banco
    - Atualizar mensagem quando busca não retorna resultados
    - Adicionar sugestão de ação para o usuário
    - _Requirements: 7.3_

- [x] 7. Checkpoint - Verificar tratamento de erros e edge cases
  - Testar comportamento com lista vazia de carreiras
  - Testar comportamento com erro de rede simulado
  - Testar busca sem resultados
  - Perguntar ao usuário se há dúvidas ou problemas

- [ ] 8. Implementar testes de integração
  - [ ]* 8.1 Configurar Mock Service Worker (MSW) para testes
    - Instalar dependência msw se necessário
    - Criar handlers para endpoint /api/careers
    - Configurar mock de respostas de sucesso e erro
    - _Requirements: 7.2_
  
  - [ ]* 8.2 Escrever testes de integração para fluxo completo
    - Testar renderização inicial com loading
    - Testar carregamento bem-sucedido de carreiras
    - Testar exibição de contagem de simulados
    - Testar filtro de busca funcionando
    - Testar navegação ao clicar em carreira
    - Testar exibição de erro quando API falha
    - _Requirements: 1.1, 3.1, 4.1, 5.1, 5.2, 5.3_
  
  - [ ]* 8.3 Escrever property test para navegação
    - **Property 6: Navigation ID Preservation**
    - **Validates: Requirements 4.1, 4.2**
    - Gerar carreiras com IDs aleatórios
    - Verificar que URL de navegação contém ID correto
    - Configurar para 100 iterações mínimas

- [x] 9. Verificar e remover código legado
  - [x] 9.1 Buscar referências a dados mockados
    - Procurar por `mockCarreiras` no código
    - Procurar por arrays hardcoded de carreiras
    - Verificar que não há dados de teste em produção
    - _Requirements: 7.1_
  
  - [x] 9.2 Documentar campo totalExams como deprecated
    - Adicionar comentário JSDoc com @deprecated
    - Indicar que exams_count deve ser usado
    - Manter campo para compatibilidade retroativa
    - _Requirements: 2.4_
  
  - [x] 9.3 Atualizar documentação do código
    - Adicionar comentários explicando estrutura de dados
    - Documentar funções helper criadas
    - Adicionar exemplos de uso quando apropriado

- [x] 10. Checkpoint final - Executar todos os testes
  - Executar suite completa de testes unitários
  - Executar todos os property tests (mínimo 100 iterações cada)
  - Executar testes de integração
  - Verificar que todos os testes passam
  - Perguntar ao usuário se está pronto para deploy

## Notes

- Tasks marcadas com `*` são opcionais e focam em testes
- Backend já está correto - todas as mudanças são no frontend
- Prioridade: sincronizar interfaces TypeScript e exibir contagem de simulados
- Property tests devem rodar mínimo 100 iterações cada
- Cada property test deve incluir comentário: `// Feature: careers-page-fix, Property N: [texto]`
- Manter compatibilidade retroativa com campo `totalExams` durante transição
- Não há necessidade de mudanças no backend (CareerRepository já está correto)
