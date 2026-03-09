# Design do Bugfix: Auto-Finalização de Tentativas Expiradas

## Overview

Este bugfix resolve o problema de tentativas de simulados que permanecem "ativas" indefinidamente após o tempo limite expirar. A solução implementa verificação server-side de expiração no método `findActiveByUserAndExam` do `AttemptRepository`, garantindo que tentativas expiradas sejam automaticamente finalizadas antes de serem retornadas. Isso previne que o timer exiba valores incorretos (ex: 927 horas) e permite que usuários iniciem novas tentativas após a expiração.

A abordagem é defensiva: qualquer consulta a uma tentativa "ativa" verificará se ela já expirou, finalizando-a automaticamente se necessário. Isso garante consistência mesmo quando o usuário fecha o navegador ou perde conexão durante uma tentativa.

## Glossary

- **Bug_Condition (C)**: A condição que dispara o bug - quando uma tentativa tem `finished_at = NULL` mas `now() - started_at > time_limit_minutes * 60`
- **Property (P)**: O comportamento desejado - tentativas expiradas devem ser auto-finalizadas com resultado parcial
- **Preservation**: Comportamentos existentes que devem permanecer inalterados - tentativas ativas dentro do prazo, finalização manual, cálculo de timer
- **AttemptRepository**: Repositório em `laravel/app/Domain/Exam/Repositories/AttemptRepository.php` que gerencia operações de tentativas
- **findActiveByUserAndExam**: Método que busca tentativa ativa de um usuário para um simulado específico
- **StartAttemptAction**: Action em `laravel/app/Domain/Exam/Actions/StartAttemptAction.php` que inicia ou retorna tentativa ativa
- **FinishAttemptAction**: Action em `laravel/app/Domain/Exam/Actions/FinishAttemptAction.php` que finaliza tentativas e calcula resultados
- **Timer Component**: Componente React em `laravel/resources/react/components/Timer.tsx` que exibe countdown
- **initialTimerSeconds**: Tempo restante em segundos calculado pelo backend e enviado ao frontend
- **time_limit_minutes**: Campo do modelo Exam que define o tempo limite do simulado (1-300 minutos)

## Bug Details

### Fault Condition

O bug se manifesta quando uma tentativa de simulado expira (tempo decorrido desde `started_at` excede `time_limit_minutes * 60` segundos) mas o usuário não está com a página aberta para que o timer do frontend finalize a tentativa. O método `findActiveByUserAndExam` retorna a tentativa expirada como se fosse ativa, pois verifica apenas `finished_at IS NULL` sem considerar o tempo decorrido.

**Formal Specification:**
```
FUNCTION isBugCondition(attempt, exam)
  INPUT: attempt of type Attempt, exam of type Exam
  OUTPUT: boolean
  
  RETURN attempt.finished_at IS NULL
         AND (now() - attempt.started_at) > (exam.time_limit_minutes * 60)
         AND attempt NOT auto-finalized by backend
END FUNCTION
```

### Examples

- **Exemplo 1**: Usuário inicia simulado de 60 minutos às 10:00, fecha navegador às 10:15. Retorna às 12:00 (2 horas depois). Sistema retorna `initialTimerSeconds = 0` mas não finaliza a tentativa, impedindo nova tentativa.

- **Exemplo 2**: Usuário inicia simulado de 120 minutos, perde conexão após 30 minutos. Retorna 5 dias depois. Sistema calcula `elapsedSeconds` muito grande, retorna `remainingSeconds = 0`, mas tentativa continua ativa. Frontend pode exibir "927 horas" dependendo de como interpreta o valor.

- **Exemplo 3**: Usuário inicia simulado de 90 minutos, responde 10 questões em 30 minutos, fecha navegador. Retorna 2 horas depois. Sistema deveria auto-finalizar com resultado parcial (10 questões respondidas), mas mantém tentativa ativa.

- **Edge Case**: Usuário inicia simulado de 1 minuto (mínimo), fecha navegador imediatamente. Retorna 2 minutos depois. Sistema deve auto-finalizar mesmo sem nenhuma resposta submetida.

## Expected Behavior

### Preservation Requirements

**Unchanged Behaviors:**
- Tentativas ativas dentro do tempo limite devem continuar calculando tempo restante corretamente
- Respostas submetidas durante tentativa ativa devem continuar sendo salvas normalmente
- Timer do frontend chegando a zero durante uso ativo deve continuar chamando `handleTimerExpire`
- Finalização manual via botão "Finalizar" deve continuar funcionando normalmente
- Histórico de tentativas deve continuar exibindo todas as tentativas (manuais, por tempo no frontend, e agora auto-finalizadas)
- Configuração de tempo limite pelo administrador (1-300 minutos) deve continuar funcionando

**Scope:**
Todas as tentativas que NÃO atendem à condição de bug (tentativas ativas dentro do prazo, tentativas já finalizadas) devem ser completamente não afetadas por este fix. Isso inclui:
- Tentativas em andamento com tempo restante
- Tentativas finalizadas manualmente pelo usuário
- Tentativas finalizadas automaticamente pelo timer do frontend
- Consultas a tentativas já finalizadas (para visualização de resultados)

## Hypothesized Root Cause

Baseado na descrição do bug e análise do código, as causas mais prováveis são:

1. **Falta de Verificação de Expiração no Backend**: O método `findActiveByUserAndExam` verifica apenas `finished_at IS NULL` sem considerar se o tempo já expirou. Não há lógica para detectar e auto-finalizar tentativas expiradas.
   - Localização: `AttemptRepository::findActiveByUserAndExam()` linha 46-54
   - Problema: Query `whereNull('finished_at')` não considera `time_limit_minutes`

2. **Cálculo de Timer Incorreto para Tentativas Antigas**: O `StartAttemptAction` calcula `remainingSeconds = max(0, totalSeconds - elapsedSeconds)`, que sempre retorna 0 para tentativas muito antigas, mas não finaliza a tentativa.
   - Localização: `StartAttemptAction::execute()` linha 35-38
   - Problema: Retorna `initialTimerSeconds = 0` mas não chama `FinishAttemptAction`

3. **Dependência Exclusiva do Frontend para Finalização**: O sistema assume que o timer do frontend sempre finalizará tentativas, mas isso falha quando o usuário fecha o navegador ou perde conexão.
   - Localização: `Timer.tsx` e `ExecuteExam.tsx`
   - Problema: Sem verificação server-side, tentativas ficam órfãs

4. **Ausência de Lógica de Auto-Finalização**: Não existe um método ou serviço dedicado para auto-finalizar tentativas expiradas com resultado parcial.
   - Problema: `FinishAttemptAction` só é chamado explicitamente via API, nunca automaticamente

## Correctness Properties

Property 1: Fault Condition - Auto-Finalização de Tentativas Expiradas

_For any_ tentativa onde `finished_at IS NULL` e `(now() - started_at) > (time_limit_minutes * 60)`, o método `findActiveByUserAndExam` SHALL detectar a expiração, auto-finalizar a tentativa registrando `finished_at`, `duration_seconds` e calculando o resultado parcial com as respostas já submetidas, e então retornar `NULL` (pois a tentativa não é mais ativa).

**Validates: Requirements 2.1, 2.2, 2.3, 2.4**

Property 2: Preservation - Comportamento de Tentativas Não-Expiradas

_For any_ tentativa onde `finished_at IS NULL` e `(now() - started_at) <= (time_limit_minutes * 60)`, o método `findActiveByUserAndExam` SHALL produzir exatamente o mesmo resultado que o código original, retornando a tentativa ativa com tempo restante calculado corretamente, preservando todo o comportamento existente de timer, respostas e finalização.

**Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5, 3.6**

## Fix Implementation

### Changes Required

Assumindo que nossa análise de causa raiz está correta:

**File**: `laravel/app/Domain/Exam/Repositories/AttemptRepository.php`

**Function**: `findActiveByUserAndExam`

**Specific Changes**:
1. **Adicionar Verificação de Expiração**: Após buscar tentativa com `finished_at IS NULL`, verificar se `(now() - started_at) > (exam.time_limit_minutes * 60)`
   - Carregar relação `exam` junto com a tentativa para acessar `time_limit_minutes`
   - Calcular `elapsedSeconds = now()->diffInSeconds($attempt->started_at)`
   - Comparar com `$attempt->exam->time_limit_minutes * 60`

2. **Auto-Finalizar Tentativa Expirada**: Se expirada, chamar lógica de finalização
   - Calcular `duration_seconds` como tempo limite completo (não tempo decorrido real)
   - Calcular resultado parcial com respostas já submetidas
   - Atualizar `finished_at = now()`, `duration_seconds`, `correct_answers`, `score`
   - Criar registro em `exam_results` para manter histórico

3. **Retornar NULL para Tentativa Expirada**: Após auto-finalização, retornar `NULL` ao invés da tentativa
   - Isso permite que `StartAttemptAction` crie uma nova tentativa
   - Mantém semântica de "tentativa ativa" consistente

4. **Extrair Lógica de Cálculo de Resultado**: Reutilizar `CalculateResultAction` existente
   - Evitar duplicação de lógica de cálculo de score
   - Garantir consistência entre finalização manual e auto-finalização

5. **Adicionar Logging**: Registrar quando tentativas são auto-finalizadas
   - Útil para debugging e monitoramento
   - Incluir `attempt_id`, `user_id`, `exam_id`, `elapsed_time`

**File**: `laravel/app/Domain/Exam/Actions/StartAttemptAction.php`

**Function**: `execute`

**Specific Changes**:
1. **Remover Lógica de `remainingSeconds = 0`**: Não é mais necessário calcular tempo restante para tentativas expiradas, pois `findActiveByUserAndExam` já as terá finalizado
   - Simplificar código removendo caso especial de `max(0, ...)`
   - Confiar que tentativas retornadas são sempre válidas (não expiradas)

**File**: `laravel/resources/react/pages/ExecuteExam.tsx`

**Function**: `initAttempt` (useEffect)

**Specific Changes**:
1. **Melhorar Tratamento de `initialTimerSeconds = 0`**: Adicionar verificação explícita
   - Se `initialTimerSeconds <= 0`, exibir mensagem clara de expiração
   - Redirecionar para tela de resultados ou lista de simulados
   - Evitar confusão do usuário com timer zerado

## Testing Strategy

### Validation Approach

A estratégia de testes segue abordagem de duas fases: primeiro, surfacear contraexemplos que demonstram o bug no código não corrigido, depois verificar que o fix funciona corretamente e preserva comportamento existente.

### Exploratory Fault Condition Checking

**Goal**: Surfacear contraexemplos que demonstram o bug ANTES de implementar o fix. Confirmar ou refutar a análise de causa raiz. Se refutarmos, precisaremos re-hipotetizar.

**Test Plan**: Escrever testes que criam tentativas expiradas (com `started_at` no passado) e verificam que `findActiveByUserAndExam` as retorna como ativas (comportamento bugado). Executar estes testes no código NÃO CORRIGIDO para observar falhas e entender a causa raiz.

**Test Cases**:
1. **Tentativa Expirada Há 1 Hora**: Criar tentativa com `started_at = now() - 2 hours` para simulado de 60 minutos. Chamar `findActiveByUserAndExam`. Esperar que retorne a tentativa (bug) ao invés de NULL. (falhará no código não corrigido)

2. **Tentativa Expirada Há Dias**: Criar tentativa com `started_at = now() - 5 days` para simulado de 120 minutos. Chamar `findActiveByUserAndExam`. Esperar que retorne a tentativa (bug). (falhará no código não corrigido)

3. **Tentativa Expirada com Respostas Parciais**: Criar tentativa com 10 respostas submetidas, `started_at = now() - 3 hours` para simulado de 90 minutos. Chamar `findActiveByUserAndExam`. Esperar que retorne a tentativa sem finalizar (bug). (falhará no código não corrigido)

4. **Tentativa Expirada por 1 Segundo**: Criar tentativa com `started_at = now() - 61 seconds` para simulado de 1 minuto. Chamar `findActiveByUserAndExam`. Esperar que retorne a tentativa (bug). (falhará no código não corrigido)

**Expected Counterexamples**:
- Tentativas expiradas são retornadas como ativas ao invés de serem auto-finalizadas
- `initialTimerSeconds` calculado como 0 mas tentativa não é finalizada
- Possíveis causas: falta de verificação de expiração, ausência de lógica de auto-finalização

### Fix Checking

**Goal**: Verificar que para todas as tentativas onde a condição de bug se aplica, a função corrigida produz o comportamento esperado.

**Pseudocode:**
```
FOR ALL attempt WHERE isBugCondition(attempt, exam) DO
  result := findActiveByUserAndExam_fixed(attempt.user_id, attempt.exam_id)
  ASSERT result IS NULL (tentativa foi auto-finalizada)
  ASSERT attempt.finished_at IS NOT NULL
  ASSERT attempt.duration_seconds = exam.time_limit_minutes * 60
  ASSERT attempt.score IS NOT NULL (resultado parcial calculado)
END FOR
```

### Preservation Checking

**Goal**: Verificar que para todas as tentativas onde a condição de bug NÃO se aplica, a função corrigida produz o mesmo resultado que a função original.

**Pseudocode:**
```
FOR ALL attempt WHERE NOT isBugCondition(attempt, exam) DO
  ASSERT findActiveByUserAndExam_original(user_id, exam_id) = findActiveByUserAndExam_fixed(user_id, exam_id)
END FOR
```

**Testing Approach**: Property-based testing é recomendado para preservation checking porque:
- Gera muitos casos de teste automaticamente através do domínio de entrada
- Captura edge cases que testes unitários manuais podem perder
- Fornece garantias fortes de que comportamento permanece inalterado para todas as tentativas não-bugadas

**Test Plan**: Observar comportamento no código NÃO CORRIGIDO primeiro para cliques de mouse e outras interações, depois escrever testes baseados em propriedades capturando esse comportamento.

**Test Cases**:
1. **Preservação de Tentativa Ativa Válida**: Observar que tentativas com tempo restante são retornadas corretamente no código não corrigido, depois escrever teste para verificar que isso continua após o fix
2. **Preservação de Cálculo de Timer**: Observar que `initialTimerSeconds` é calculado corretamente para tentativas ativas no código não corrigido, depois escrever teste para verificar que isso continua após o fix
3. **Preservação de Finalização Manual**: Observar que finalização via botão funciona no código não corrigido, depois escrever teste para verificar que isso continua após o fix
4. **Preservação de Histórico**: Observar que tentativas finalizadas aparecem no histórico no código não corrigido, depois escrever teste para verificar que tentativas auto-finalizadas também aparecem

### Unit Tests

- Testar `findActiveByUserAndExam` com tentativa expirada (deve auto-finalizar e retornar NULL)
- Testar `findActiveByUserAndExam` com tentativa ativa válida (deve retornar tentativa)
- Testar `findActiveByUserAndExam` com tentativa já finalizada (deve retornar NULL)
- Testar edge case de tentativa expirando exatamente no limite (60:00 vs 60:01)
- Testar auto-finalização com 0 respostas submetidas
- Testar auto-finalização com respostas parciais (calcular score correto)
- Testar que `exam_results` é criado após auto-finalização
- Testar que `StartAttemptAction` cria nova tentativa após auto-finalização da anterior

### Property-Based Tests

- Gerar tentativas aleatórias com `started_at` variando de 0 a 10 dias no passado e `time_limit_minutes` variando de 1 a 300, verificar que tentativas expiradas são sempre auto-finalizadas
- Gerar tentativas aleatórias ativas (dentro do prazo) e verificar que comportamento de preservação se mantém (mesmo resultado que código original)
- Gerar configurações aleatórias de simulados e verificar que cálculo de tempo restante está sempre correto para tentativas ativas
- Testar que múltiplas chamadas a `findActiveByUserAndExam` para mesma tentativa expirada são idempotentes (primeira finaliza, demais retornam NULL)

### Integration Tests

- Testar fluxo completo: usuário inicia simulado, fecha navegador, retorna após expiração, sistema auto-finaliza e permite nova tentativa
- Testar que timer do frontend exibe mensagem clara quando recebe `initialTimerSeconds = 0` de tentativa recém-finalizada
- Testar que histórico de tentativas mostra tentativas auto-finalizadas com indicação clara de "tempo esgotado"
- Testar que resultado parcial de tentativa auto-finalizada é calculado corretamente e exibido na tela de resultados
