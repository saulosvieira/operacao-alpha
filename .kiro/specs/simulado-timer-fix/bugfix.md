# Documento de Requisitos do Bugfix

## Introdução

O timer de tentativas de simulados não respeita o tempo limite configurado no simulado. Quando um usuário inicia uma tentativa e o tempo expira (por exemplo, o usuário fecha o navegador ou perde conexão), a tentativa permanece "ativa" indefinidamente no backend. Ao retornar ao simulado, o timer exibe valores incorretos (ex: 927 horas) ao invés de reconhecer que o tempo já expirou. O sistema precisa:

1. Detectar e auto-finalizar tentativas expiradas no backend
2. Garantir que o timer do frontend respeite o tempo limite configurado
3. Manter o histórico de tentativas para que o usuário saiba quantas vezes tentou o simulado

## Bug Analysis

### Current Behavior (Defect)

1.1 WHEN o tempo limite de uma tentativa expira e o usuário não está com a página aberta (ex: fechou o navegador) THEN o sistema mantém a tentativa como "ativa" (sem `finished_at`) indefinidamente, pois não há verificação server-side de expiração

1.2 WHEN o usuário retorna a um simulado com uma tentativa expirada THEN o sistema retorna `initialTimerSeconds = 0` mas não auto-finaliza a tentativa, impedindo o usuário de iniciar uma nova tentativa para o mesmo simulado

1.3 WHEN o backend calcula o tempo restante via `now()->diffInSeconds($attempt->startedAt)` para uma tentativa muito antiga THEN o sistema retorna `remainingSeconds = 0` mas a tentativa continua ativa, e o frontend pode exibir valores incorretos de tempo (ex: 927 horas) dependendo de como o dado é interpretado

1.4 WHEN o usuário tenta iniciar uma nova tentativa de um simulado que possui uma tentativa expirada mas não finalizada THEN o sistema retorna a tentativa expirada ao invés de criar uma nova, pois `findActiveByUserAndExam` não verifica se o tempo já expirou

1.5 WHEN o frontend recebe `initialTimerSeconds = 0` para uma tentativa expirada THEN o componente Timer tenta chamar `onExpire` imediatamente, mas a experiência do usuário é confusa pois não há feedback claro de que a tentativa anterior expirou

### Expected Behavior (Correct)

2.1 WHEN o tempo limite de uma tentativa expira THEN o sistema SHALL auto-finalizar a tentativa no backend ao detectar a expiração (durante qualquer consulta à tentativa), registrando `finished_at`, `duration_seconds` e calculando o resultado parcial com as respostas já submetidas

2.2 WHEN o usuário retorna a um simulado com uma tentativa que já expirou THEN o sistema SHALL mostrar o resultado da tentativa expirada e permitir que o usuário inicie uma nova tentativa

2.3 WHEN o backend verifica tentativas ativas via `findActiveByUserAndExam` THEN o sistema SHALL considerar como "expirada" qualquer tentativa onde `now() - started_at > exam.time_limit_minutes * 60`, e auto-finalizá-la antes de retornar

2.4 WHEN o usuário inicia uma nova tentativa após uma tentativa expirada ser auto-finalizada THEN o sistema SHALL criar uma nova tentativa com o timer completo (tempo_limite_minutos * 60 segundos), mantendo o histórico da tentativa anterior

2.5 WHEN o frontend recebe uma tentativa com `initialTimerSeconds = 0` ou negativo THEN o sistema SHALL exibir uma mensagem clara informando que o tempo expirou e redirecionar o usuário para a tela de resultados

### Unchanged Behavior (Regression Prevention)

3.1 WHEN uma tentativa está ativa e dentro do tempo limite THEN o sistema SHALL CONTINUE TO calcular corretamente o tempo restante como `(time_limit_minutes * 60) - elapsed_seconds` e exibir o countdown no frontend

3.2 WHEN o usuário responde questões durante uma tentativa ativa THEN o sistema SHALL CONTINUE TO salvar as respostas normalmente e atualizar o estado da tentativa

3.3 WHEN o timer do frontend chega a zero durante uso ativo THEN o sistema SHALL CONTINUE TO chamar `handleTimerExpire` que finaliza a tentativa via API e redireciona para resultados

3.4 WHEN o usuário finaliza manualmente uma tentativa (botão "Finalizar") THEN o sistema SHALL CONTINUE TO calcular o resultado e registrar a tentativa normalmente

3.5 WHEN o usuário consulta o histórico de tentativas THEN o sistema SHALL CONTINUE TO exibir todas as tentativas (finalizadas manualmente, por tempo esgotado no frontend, e agora também auto-finalizadas por expiração no backend)

3.6 WHEN o administrador configura o tempo limite de um simulado (1-300 minutos) THEN o sistema SHALL CONTINUE TO validar e persistir o valor corretamente
