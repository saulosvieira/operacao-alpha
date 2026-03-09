# Plano de Implementação

- [x] 1. Escrever teste de exploração da condição de bug
  - **Property 1: Fault Condition** - Tentativas Expiradas Permanecem Ativas
  - **CRÍTICO**: Este teste DEVE FALHAR no código não corrigido - a falha confirma que o bug existe
  - **NÃO tente corrigir o teste ou o código quando ele falhar**
  - **NOTA**: Este teste codifica o comportamento esperado - ele validará o fix quando passar após a implementação
  - **OBJETIVO**: Surfacear contraexemplos que demonstram que o bug existe
  - **Abordagem PBT Escopo**: Para bugs determinísticos, escopar a propriedade aos casos concretos de falha para garantir reprodutibilidade
  - Testar que `findActiveByUserAndExam` retorna tentativas expiradas como ativas (comportamento bugado)
  - Casos de teste:
    - Tentativa expirada há 1 hora (started_at = now() - 2 hours, time_limit = 60 min)
    - Tentativa expirada há dias (started_at = now() - 5 days, time_limit = 120 min)
    - Tentativa expirada com respostas parciais (10 respostas, started_at = now() - 3 hours, time_limit = 90 min)
    - Tentativa expirada por 1 segundo (started_at = now() - 61 seconds, time_limit = 1 min)
  - As asserções do teste devem corresponder às Expected Behavior Properties do design
  - Executar teste no código NÃO CORRIGIDO
  - **RESULTADO ESPERADO**: Teste FALHA (isso está correto - prova que o bug existe)
  - Documentar contraexemplos encontrados para entender a causa raiz
  - Marcar tarefa como completa quando o teste estiver escrito, executado e a falha documentada
  - _Requirements: 1.1, 1.2, 1.3, 1.4_

- [x] 2. Escrever testes de propriedade de preservação (ANTES de implementar o fix)
  - **Property 2: Preservation** - Comportamento de Tentativas Não-Expiradas
  - **IMPORTANTE**: Seguir metodologia observation-first
  - Observar comportamento no código NÃO CORRIGIDO para inputs não-bugados
  - Escrever testes baseados em propriedades capturando os padrões de comportamento observados dos Preservation Requirements
  - Property-based testing gera muitos casos de teste para garantias mais fortes
  - Casos de teste:
    - Tentativas ativas dentro do tempo limite retornam corretamente com tempo restante calculado
    - Cálculo de `initialTimerSeconds` está correto para tentativas ativas
    - Respostas submetidas durante tentativa ativa são salvas normalmente
    - Finalização manual via botão "Finalizar" funciona normalmente
    - Histórico de tentativas exibe todas as tentativas (manuais, por tempo no frontend)
    - Configuração de tempo limite pelo administrador (1-300 minutos) funciona corretamente
  - Executar testes no código NÃO CORRIGIDO
  - **RESULTADO ESPERADO**: Testes PASSAM (isso confirma o comportamento baseline a preservar)
  - Marcar tarefa como completa quando os testes estiverem escritos, executados e passando no código não corrigido
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6_

- [x] 3. Fix para auto-finalização de tentativas expiradas

  - [x] 3.1 Implementar verificação de expiração no AttemptRepository
    - Modificar `findActiveByUserAndExam` em `laravel/app/Domain/Exam/Repositories/AttemptRepository.php`
    - Carregar relação `exam` junto com a tentativa para acessar `time_limit_minutes`
    - Após buscar tentativa com `finished_at IS NULL`, verificar se `(now() - started_at) > (exam.time_limit_minutes * 60)`
    - Calcular `elapsedSeconds = now()->diffInSeconds($attempt->started_at)`
    - Comparar com `$attempt->exam->time_limit_minutes * 60`
    - _Bug_Condition: isBugCondition(attempt, exam) onde attempt.finished_at IS NULL AND (now() - attempt.started_at) > (exam.time_limit_minutes * 60)_
    - _Expected_Behavior: Auto-finalizar tentativa registrando finished_at, duration_seconds e calculando resultado parcial, depois retornar NULL_
    - _Preservation: Tentativas ativas dentro do prazo devem continuar retornando normalmente com tempo restante calculado corretamente_
    - _Requirements: 2.1, 2.3_

  - [x] 3.2 Implementar lógica de auto-finalização
    - Se tentativa expirada, calcular `duration_seconds` como tempo limite completo
    - Reutilizar `CalculateResultAction` existente para calcular resultado parcial com respostas já submetidas
    - Atualizar `finished_at = now()`, `duration_seconds`, `correct_answers`, `score`
    - Criar registro em `exam_results` para manter histórico
    - Adicionar logging quando tentativas são auto-finalizadas (incluir attempt_id, user_id, exam_id, elapsed_time)
    - _Requirements: 2.1, 2.2_

  - [x] 3.3 Retornar NULL após auto-finalização
    - Após auto-finalizar tentativa expirada, retornar `NULL` ao invés da tentativa
    - Isso permite que `StartAttemptAction` crie uma nova tentativa
    - Mantém semântica de "tentativa ativa" consistente
    - _Requirements: 2.3, 2.4_

  - [x] 3.4 Simplificar StartAttemptAction
    - Modificar `execute` em `laravel/app/Domain/Exam/Actions/StartAttemptAction.php`
    - Remover lógica de `remainingSeconds = max(0, ...)` pois tentativas expiradas já terão sido finalizadas
    - Confiar que tentativas retornadas por `findActiveByUserAndExam` são sempre válidas (não expiradas)
    - _Requirements: 2.4_

  - [x] 3.5 Melhorar tratamento no frontend
    - Modificar `initAttempt` (useEffect) em `laravel/resources/react/pages/ExecuteExam.tsx`
    - Adicionar verificação explícita: se `initialTimerSeconds <= 0`, exibir mensagem clara de expiração
    - Redirecionar para tela de resultados ou lista de simulados
    - Evitar confusão do usuário com timer zerado
    - _Requirements: 2.5_

  - [x] 3.6 Verificar que teste de exploração da condição de bug agora passa
    - **Property 1: Expected Behavior** - Tentativas Expiradas São Auto-Finalizadas
    - **IMPORTANTE**: Re-executar o MESMO teste da tarefa 1 - NÃO escrever um novo teste
    - O teste da tarefa 1 codifica o comportamento esperado
    - Quando este teste passar, confirma que o comportamento esperado está satisfeito
    - Executar teste de exploração da condição de bug do passo 1
    - **RESULTADO ESPERADO**: Teste PASSA (confirma que o bug está corrigido)
    - Verificar que `findActiveByUserAndExam` agora retorna NULL para tentativas expiradas
    - Verificar que tentativas expiradas têm `finished_at`, `duration_seconds` e `score` preenchidos
    - _Requirements: 2.1, 2.2, 2.3, 2.4_

  - [x] 3.7 Verificar que testes de preservação ainda passam
    - **Property 2: Preservation** - Comportamento de Tentativas Não-Expiradas Preservado
    - **IMPORTANTE**: Re-executar os MESMOS testes da tarefa 2 - NÃO escrever novos testes
    - Executar testes de propriedade de preservação do passo 2
    - **RESULTADO ESPERADO**: Testes PASSAM (confirma que não há regressões)
    - Confirmar que todos os testes ainda passam após o fix (sem regressões)
    - Verificar que tentativas ativas dentro do prazo continuam funcionando normalmente
    - Verificar que finalização manual, histórico e configuração de tempo limite não foram afetados

- [x] 4. Checkpoint - Garantir que todos os testes passam
  - Executar suite completa de testes (unit, property-based, integration)
  - Verificar que testes de exploração (Property 1) agora passam
  - Verificar que testes de preservação (Property 2) continuam passando
  - Verificar que não há regressões em funcionalidades existentes
  - Perguntar ao usuário se surgirem dúvidas
