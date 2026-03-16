# Plano de Implementação: Painel Admin de Simulados Respondidos

## Visão Geral

Implementação incremental do painel administrativo de simulados respondidos, seguindo a arquitetura DDD existente. Começa pela infraestrutura do domínio `Complaint` (migration, enums, model), depois as actions do domínio `Exam` para listagem/detalhe/estatísticas, controllers, rotas, views Blade (AdminLTE) e exportação CSV. Cada tarefa constrói sobre a anterior.

## Tarefas

- [x] 1. Infraestrutura do domínio Complaint
  - [x] 1.1 Criar enums `ComplaintType`, `ComplaintPriority` e `ComplaintStatus`
    - Criar `app/Domain/Complaint/Enums/ComplaintType.php` com cases: `INCORRECT_ANSWER`, `AMBIGUOUS_STATEMENT`, `OUTDATED_QUESTION`, `FORMATTING_ERROR`, `OTHER`
    - Criar `app/Domain/Complaint/Enums/ComplaintPriority.php` com cases: `LOW`, `MEDIUM`, `HIGH`
    - Criar `app/Domain/Complaint/Enums/ComplaintStatus.php` com cases: `OPEN`, `IN_REVIEW`, `RESOLVED`, `REJECTED`
    - _Requisitos: 4.2, 4.5_

  - [x] 1.2 Criar migration e model `Complaint`
    - Criar migration para tabela `complaints` com colunas: `id`, `question_id` (FK), `admin_id` (FK), `type`, `description`, `priority`, `status` (default 'open'), `resolution_note` (nullable), `resolved_at` (nullable), timestamps
    - Criar índices em: `question_id`, `status`, `type`, `priority`, `created_at`
    - Criar `app/Domain/Complaint/Models/Complaint.php` com fillable, casts (enums), relações `question()` e `admin()`, método `isPending()`
    - _Requisitos: 4.3, 4.5, 4.9_

  - [x] 1.3 Criar `ComplaintRepository`
    - Criar `app/Domain/Complaint/Repositories/ComplaintRepository.php`
    - Implementar métodos: `create()`, `update()`, `findOrFail()`, `paginate()` com filtros (status, type, priority), `countPendingByExamId()`, `countPendingByQuestionIds()`
    - _Requisitos: 4.3, 4.4, 4.6, 4.7, 4.8, 6.1, 6.3_

  - [x] 1.4 Criar DTOs do domínio Exam para admin
    - Criar `app/Domain/Exam/DTOs/Admin/AttemptListItemData.php` com campos: id, userName, examTitle, careerName, score, correctAnswers, totalQuestions, durationMinutes, finishedAt, pendingComplaints
    - Criar `app/Domain/Exam/DTOs/Admin/AttemptDetailData.php` com campos: id, userName, userEmail, examTitle, careerName, score, correctAnswers, totalQuestions, durationMinutes, finishedAt, examId
    - Criar `app/Domain/Exam/DTOs/Admin/QuestionAnswerData.php` com campos: questionId, questionNumber, statement, selectedOption, correctAnswer, isCorrect, optionA-E, explanation, accuracyRate, hasPendingComplaints
    - Criar `app/Domain/Exam/DTOs/Admin/ExamStatisticsData.php` com campos: totalAttempts, avgScore, avgAccuracy, avgDurationMinutes
    - _Requisitos: 1.3, 2.2, 2.3, 3.1_

- [x] 2. Checkpoint — Verificar infraestrutura base
  - Garantir que a migration roda corretamente, que os enums, model e repository estão funcionais. Rodar todos os testes existentes. Perguntar ao usuário se há dúvidas.

- [x] 3. Actions do domínio Exam para admin (listagem e estatísticas)
  - [x] 3.1 Criar `ListAttemptsForAdminAction`
    - Criar `app/Domain/Exam/Actions/Admin/ListAttemptsForAdminAction.php`
    - Implementar `execute(array $filters, int $perPage = 15): LengthAwarePaginator` com filtros: search (nome usuário/título simulado), exam_id, career_id, date_from, date_to
    - Implementar `count(array $filters): int` para confirmação de exportação
    - Query deve retornar apenas tentativas com `finished_at` não nulo, ordenadas por `finished_at` desc
    - Incluir subquery para contagem de reclamações pendentes por simulado
    - Usar joins com `users`, `exams`, `careers` para dados da listagem
    - _Requisitos: 1.2, 1.3, 1.4, 1.5, 1.6, 1.7, 1.8, 6.1, 6.2_

  - [ ]* 3.2 Escrever testes de propriedade para ListAttemptsForAdminAction
    - **Propriedade 2: Ordenação decrescente de tentativas por data de finalização** — Valida: Requisito 1.2
    - **Propriedade 3: Apenas tentativas finalizadas na listagem** — Valida: Requisito 1.8
    - **Propriedade 4: Filtros de tentativas retornam apenas resultados correspondentes** — Valida: Requisitos 1.4, 1.5, 1.6, 1.7
    - **Propriedade 5: Campos obrigatórios na listagem de tentativas** — Valida: Requisito 1.3
    - **Propriedade 15: Indicador de reclamações pendentes por simulado** — Valida: Requisitos 6.1, 6.2

  - [x] 3.3 Criar `GetExamStatisticsAction`
    - Criar `app/Domain/Exam/Actions/Admin/GetExamStatisticsAction.php`
    - Implementar `execute(array $filters): array` retornando: total, avgScore, avgAccuracy, avgDurationMinutes
    - Calcular via queries agregadas (`AVG`, `COUNT`, `SUM`) para performance
    - Tratar divisão por zero quando não há tentativas (retornar zeros)
    - Aplicar os mesmos filtros da listagem de tentativas
    - _Requisitos: 3.1, 3.2_

  - [ ]* 3.4 Escrever teste de propriedade para GetExamStatisticsAction
    - **Propriedade 7: Estatísticas calculadas corretamente sobre tentativas filtradas** — Valida: Requisitos 3.1, 3.2

- [x] 4. Actions do domínio Exam para admin (detalhe e exportação)
  - [x] 4.1 Criar `GetAttemptDetailAction`
    - Criar `app/Domain/Exam/Actions/Admin/GetAttemptDetailAction.php`
    - Implementar `execute(int $attemptId): array` retornando: attempt (AttemptDetailData), answers (Collection de QuestionAnswerData), questionStats, complaints
    - Calcular taxa de acerto por questão com base em todas as tentativas finalizadas do simulado
    - Marcar questões com taxa de acerto < 30% como potencialmente problemáticas
    - Incluir indicador de reclamações pendentes por questão via `ComplaintRepository::countPendingByQuestionIds()`
    - Retornar 404 se tentativa não encontrada ou não finalizada
    - _Requisitos: 2.1, 2.2, 2.3, 2.4, 2.5, 3.3, 3.4, 6.3_

  - [ ]* 4.2 Escrever testes de propriedade para GetAttemptDetailAction
    - **Propriedade 6: Campos obrigatórios no detalhe da tentativa** — Valida: Requisitos 2.2, 2.3
    - **Propriedade 8: Taxa de acerto por questão calculada corretamente** — Valida: Requisito 3.3
    - **Propriedade 9: Questões com taxa de acerto inferior a 30% marcadas como problemáticas** — Valida: Requisito 3.4
    - **Propriedade 16: Indicador de reclamações pendentes por questão no detalhe** — Valida: Requisito 6.3

  - [x] 4.3 Criar `ExportAttemptsCsvAction`
    - Criar `app/Domain/Exam/Actions/Admin/ExportAttemptsCsvAction.php`
    - Implementar `execute(array $filters): StreamedResponse` gerando CSV com campos: ID, Nome do Usuário, E-mail, Título do Simulado, Carreira, Nota, Acertos, Total Questões, Duração (min), Data Finalização
    - Usar `cursor()` para eficiência de memória em grandes volumes
    - Aplicar os mesmos filtros da listagem de tentativas
    - Retornar CSV com apenas cabeçalho quando não há tentativas
    - _Requisitos: 5.1, 5.2, 5.3_

  - [ ]* 4.4 Escrever teste de propriedade para ExportAttemptsCsvAction
    - **Propriedade 14: CSV contém exatamente as tentativas filtradas com campos obrigatórios** — Valida: Requisitos 5.2, 5.3

- [x] 5. Checkpoint — Verificar actions do domínio Exam
  - Garantir que todas as actions retornam dados corretos. Rodar todos os testes. Perguntar ao usuário se há dúvidas.

- [x] 6. Actions do domínio Complaint
  - [x] 6.1 Criar `CreateComplaintAction`
    - Criar `app/Domain/Complaint/Actions/CreateComplaintAction.php`
    - Implementar `execute(int $questionId, int $adminId, ComplaintType $type, string $description, ComplaintPriority $priority): Complaint`
    - Validar que a questão existe, persistir com status inicial `OPEN`
    - _Requisitos: 4.1, 4.2, 4.3_

  - [ ]* 6.2 Escrever teste de propriedade para CreateComplaintAction
    - **Propriedade 10: Persistência de reclamação com dados obrigatórios** — Valida: Requisito 4.3

  - [x] 6.3 Criar `UpdateComplaintStatusAction`
    - Criar `app/Domain/Complaint/Actions/UpdateComplaintStatusAction.php`
    - Implementar `execute(int $complaintId, ComplaintStatus $status, ?string $resolutionNote = null): Complaint`
    - Registrar `resolved_at` quando status muda para `RESOLVED` ou `REJECTED`
    - Retornar 404 se reclamação não encontrada
    - _Requisitos: 4.9_

  - [ ]* 6.4 Escrever teste de propriedade para UpdateComplaintStatusAction
    - **Propriedade 13: Atualização de status registra data e nota de resolução** — Valida: Requisito 4.9

  - [x] 6.5 Criar `ListComplaintsAction`
    - Criar `app/Domain/Complaint/Actions/ListComplaintsAction.php`
    - Implementar `execute(array $filters, int $perPage = 15): LengthAwarePaginator` com filtros: status, type, priority
    - Ordenar por `created_at` desc, incluir joins com `questions`, `exams`, `users`
    - _Requisitos: 4.4, 4.5, 4.6, 4.7, 4.8_

  - [ ]* 6.6 Escrever testes de propriedade para ListComplaintsAction
    - **Propriedade 11: Filtros de reclamações retornam apenas resultados correspondentes** — Valida: Requisitos 4.6, 4.7, 4.8
    - **Propriedade 12: Ordenação decrescente de reclamações por data de criação** — Valida: Requisito 4.4

- [x] 7. Controllers e rotas admin
  - [x] 7.1 Criar `AttemptController`
    - Criar `app/Http/Controllers/Admin/AttemptController.php`
    - Implementar `index(Request $request, ListAttemptsForAdminAction, GetExamStatisticsAction): View` com filtros e estatísticas
    - Implementar `show(int $attemptId, GetAttemptDetailAction): View` com detalhe da tentativa
    - Implementar `export(Request $request, ExportAttemptsCsvAction): StreamedResponse` para download CSV
    - Implementar `exportCount(Request $request, ListAttemptsForAdminAction): JsonResponse` para confirmação antes de exportar
    - Usar `$this->authorize('admin')` seguindo padrão do `WebhookHistoryController`
    - _Requisitos: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6, 1.7, 2.1, 2.2, 5.1, 5.4_

  - [x] 7.2 Criar `ComplaintController`
    - Criar `app/Http/Controllers/Admin/ComplaintController.php`
    - Implementar `index(Request $request, ListComplaintsAction): View` com filtros e paginação
    - Implementar `store(Request $request, CreateComplaintAction): RedirectResponse` com validação de inputs
    - Implementar `updateStatus(Request $request, int $complaintId, UpdateComplaintStatusAction): RedirectResponse`
    - Usar `$this->authorize('admin')` em todos os métodos
    - _Requisitos: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7, 4.8, 4.9, 4.10_

  - [x] 7.3 Registrar rotas admin para tentativas e reclamações
    - Adicionar rotas de tentativas em `routes/web.php` dentro do grupo admin autenticado: `GET /admin/attempts`, `GET /admin/attempts/export`, `GET /admin/attempts/export/count`, `GET /admin/attempts/{attempt}`
    - Adicionar rotas de reclamações: `GET /admin/complaints`, `POST /admin/complaints`, `PATCH /admin/complaints/{complaint}/status`
    - _Requisitos: 1.1, 4.1_

  - [ ]* 7.4 Escrever teste de propriedade para acesso restrito a administradores
    - **Propriedade 1: Acesso restrito a administradores** — Valida: Requisito 1.1
    - Testar que usuários não-admin recebem 403 ou redirect ao acessar `/admin/attempts` e `/admin/complaints`
    - Testar que administradores autenticados acessam com sucesso (HTTP 200)

- [x] 8. Views Blade — Listagem de tentativas
  - [x] 8.1 Criar view `resources/views/admin/attempts/index.blade.php`
    - Estender `adminlte::page`, seguindo padrão das views existentes (webhooks/edduz/index)
    - Painel de estatísticas resumidas no topo: total de tentativas, nota média, taxa média de acerto, tempo médio
    - Formulário de filtros: campo de busca (nome/título), select de simulado, select de carreira, date_from, date_to
    - Tabela paginada com colunas: ID, Usuário, Simulado (com indicador de reclamações pendentes), Carreira, Nota, Acertos/Total, Duração, Data Finalização, Ações (ver detalhe)
    - Botão de exportação CSV com confirmação via AJAX quando > 10.000 registros
    - _Requisitos: 1.2, 1.3, 1.4, 1.5, 1.6, 1.7, 3.1, 3.2, 5.1, 5.4, 6.1, 6.2_

  - [x] 8.2 Criar view `resources/views/admin/attempts/show.blade.php`
    - Card com informações resumidas da tentativa: nome, email, simulado, carreira, nota, acertos, total, duração, data
    - Tabela de respostas com colunas: nº questão, enunciado resumido, opção selecionada, resposta correta, correto/incorreto (badge verde/vermelho), taxa de acerto, indicador de reclamação
    - Destaque visual para respostas incorretas (cor diferenciada)
    - Destaque visual para questões com taxa de acerto < 30% (badge "potencialmente problemática")
    - Seção expansível por questão com enunciado completo, todas as alternativas, explicação e botão para registrar reclamação
    - Link direto para edição da questão quando há reclamação de gabarito incorreto
    - _Requisitos: 2.2, 2.3, 2.4, 2.5, 3.3, 3.4, 4.1, 4.10, 6.3_

- [x] 9. Views Blade — Reclamações
  - [x] 9.1 Criar view `resources/views/admin/complaints/index.blade.php`
    - Estender `adminlte::page`, seguindo padrão das views existentes
    - Formulário de filtros: select de status, select de tipo, select de prioridade
    - Tabela paginada com colunas: ID, Simulado, Nº Questão, Tipo, Prioridade, Status (badge colorido), Administrador, Data Criação, Ações (atualizar status)
    - Modal ou formulário inline para atualizar status com campo de nota de resolução
    - _Requisitos: 4.4, 4.5, 4.6, 4.7, 4.8, 4.9_

  - [x] 9.2 Criar modal/formulário de registro de reclamação na view de detalhe
    - Modal com campos: tipo (select com opções do enum), descrição (textarea), prioridade (select)
    - Submissão via POST para `/admin/complaints` com `question_id` como hidden field
    - Feedback visual via session flash messages seguindo padrão AdminLTE
    - _Requisitos: 4.1, 4.2, 4.3_

- [x] 10. Checkpoint — Verificar views e fluxo completo
  - Garantir que todas as views renderizam corretamente, filtros funcionam, paginação funciona, exportação CSV funciona. Rodar todos os testes. Perguntar ao usuário se há dúvidas.

- [x] 11. Checkpoint final — Validação completa
  - Rodar todos os testes (existentes e novos). Verificar que todas as rotas estão registradas corretamente. Verificar que a migration roda sem erros. Verificar navegação completa: listagem → detalhe → reclamação → listagem de reclamações. Perguntar ao usuário se há dúvidas.

## Notas

- Tarefas marcadas com `*` são opcionais e podem ser puladas para um MVP mais rápido
- Cada tarefa referencia requisitos específicos para rastreabilidade
- Checkpoints garantem validação incremental
- Testes de propriedade validam propriedades universais de corretude
- Testes unitários validam exemplos específicos e edge cases
- O projeto usa Pest PHP sobre PHPUnit com banco SQLite em memória para testes
- Views seguem o padrão AdminLTE com Blade templates já estabelecido no projeto
