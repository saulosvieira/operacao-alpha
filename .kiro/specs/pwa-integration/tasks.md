# Plano de Implementação - Integração PWA React/Laravel

## Visão Geral

Este plano implementa a integração do frontend React/Vite ao backend Laravel, seguindo arquitetura limpa por features com Actions single-purpose, Repositories e DTOs.

## Status Atual

✅ **Concluído:**
- Estrutura de diretórios Domain criada com todas as features
- Models movidos para Domain/{Feature}/Models (User, Career, Exam, Question, etc)
- Estrutura de Controllers organizada (Auth, Api, Admin)
- Migrations criadas para renomear tabelas/colunas para inglês
- Enums implementados (UserRole, SubscriptionStatus, AnswerOption, ExamStatus, RankingType)
- DTOs, Actions e Repositories implementados para todas as features
- APIs RESTful completas para Auth, Career, Exam, Ranking, Performance, Approved, Subscription, Notification, User
- Frontend React movido para Laravel (resources/react/)
- Vite configurado para integração com Laravel
- Serviços de API criados no frontend
- Stores atualizados para usar APIs reais
- Componentes e páginas atualizados
- Seeders completos criados (User, Career, Notice, Exam, Question, Attempt, Ranking, Approved)
- Controllers Admin atualizados para usar novos nomes em inglês
- PWA manifest e ícones criados
- Service Worker criado para notificações
- Meta tags PWA adicionadas no Blade
- Service Worker registrado no frontend (main.tsx)
- Docker configurado com Node.js, npm e build de produção
- Docker Compose configurado com serviço Vite para desenvolvimento
- Lazy loading implementado em todas as rotas
- Compressão Gzip configurada no Nginx
- CSRF token configurado no Axios
- README.md completo com documentação
- Build de produção testado e funcionando

🔄 **Em Progresso:**
- Nenhuma tarefa em andamento

⏳ **Próximos Passos Críticos:**

**Fase 1 - Configuração de Segurança (Essencial para funcionamento):**
1. Configurar CORS para permitir requisições do frontend (Task 22.1)
2. Configurar Rate Limiting para proteger APIs (Task 22.2)
3. Atualizar Sanctum stateful domains para incluir localhost:8090 e localhost:5173 (Task 22.4)

**Fase 2 - Validação e Testes (Garantir qualidade):**
4. Testar aplicação completa end-to-end (Task 23)
5. Validar PWA com Lighthouse (Task 24.2)
6. Testar instalação PWA em dispositivos móveis (Task 24.5)

**Opcional:**
- Escrever testes de integração para APIs críticas (Task 24.3)

## Resumo de Tarefas

**Total:** 25 tarefas principais (1 opcional)
- ✅ **Concluídas:** 22 (Tasks 1-21, 22.3, 24.1, 24.4)
- ⏳ **Pendentes Obrigatórias:** 6 (Tasks 22.1, 22.2, 22.4, 23, 24.2, 24.5)
- ⏳ **Pendentes Opcionais:** 1 (Task 24.3 - Testes de integração)
- 📊 **Progresso:** ~88% (92% se excluir opcionais)

**Estimativa de Esforço Restante:**
- Security Configuration (Tasks 22.1, 22.2, 22.4): ~3% do trabalho
- End-to-End Testing (Task 23): ~3% do trabalho
- PWA Validation (Tasks 24.2, 24.5): ~4% do trabalho
- Integration Tests - Opcional (Task 24.3): ~2% do trabalho

## Tarefas

- [x] 1. Reorganizar estrutura do backend para arquitetura por features
  - Criar estrutura de diretórios Domain/{Feature}
  - Mover Models existentes para Domain/{Feature}/Models
  - Criar namespaces para DTOs, Actions, Repositories
  - _Requisitos: 12.1-12.8_

- [x] 1.1 Criar estrutura base de diretórios
  - Criar app/Domain com subpastas para cada feature
  - Criar estrutura Http/Controllers organizada (Auth, Api, Admin)
  - _Requisitos: 12.1-12.8_

- [x] 1.2 Mover e renomear Models existentes
  - Mover User para Domain/Auth/Models
  - Mover Carreira para Domain/Career/Models (renomear para Career)
  - Mover Simulado para Domain/Exam/Models (renomear para Exam)
  - Mover Questao para Domain/Exam/Models (renomear para Question)
  - Mover demais models para features apropriadas
  - _Requisitos: 12.1-12.8_

- [x] 2. Criar migrations para renomear tabelas e colunas para inglês
  - Criar migration para renomear tabela carreiras → careers (colunas: nome→name, descricao→description, ativa→active)
  - Criar migration para renomear tabela editais → notices (colunas: carreira_id→career_id, titulo→title, descricao→description, data_prova→exam_date)
  - Criar migration para renomear tabela simulados → exams (colunas: carreira_id→career_id, titulo→title, descricao→description, ativo→active)
  - Criar migration para renomear tabela questoes → questions (colunas: simulado_id→exam_id, enunciado→statement, imagem_enunciado→statement_image, alternativa_*→option_*, imagem_alternativa_*→option_*_image, resposta_correta→correct_answer, explicacao→explanation)
  - Criar migration para renomear tabela respostas_usuarios → user_answers (colunas: usuario_id→user_id, simulado_id→exam_id, questao_id→question_id, resposta_escolhida→chosen_answer, correta→correct, tempo_segundos→time_seconds)
  - Criar migration para renomear tabela resultados_simulados → exam_results (colunas: usuario_id→user_id, simulado_id→exam_id, total_questoes→total_questions, acertos→correct_answers, nota_final→final_score, tempo_total_segundos→total_time_seconds)
  - Criar migration para renomear tabela aprovados → approved (colunas: nome→name, carreira→career, observacao→note, foto_url→avatar_url)
  - Criar migration para adicionar tabela attempts (id, user_id, exam_id, started_at, finished_at, duration_seconds, correct_answers, score, timestamps)
  - Criar migration para adicionar tabela notification_subscriptions (id, user_id, endpoint, public_key, auth_token, timestamps)
  - Atualizar todos os Models para refletir novos nomes de tabelas e colunas
  - Atualizar relacionamentos nos Models (ex: carreira() → career())
  - _Requisitos: 12.1-12.8_
  - _Nota: Após criar todas as migrations, executar php artisan migrate:fresh --seed para recriar banco_

- [x] 3. Criar Enums para o sistema
  - Criar Domain/Auth/Enums/UserRole
  - Criar Domain/Auth/Enums/SubscriptionStatus
  - Criar Domain/Exam/Enums/AnswerOption
  - Criar Domain/Exam/Enums/ExamStatus
  - Criar Domain/Ranking/Enums/RankingType
  - _Requisitos: 12.1-12.8_

- [x] 4. Implementar feature Auth
  - _Requisitos: 3.1-3.5_

- [x] 4.1 Criar DTOs de Auth
  - Criar Domain/Auth/DTOs/UserData
  - Criar Domain/Auth/DTOs/LoginData
  - Criar Domain/Auth/DTOs/RegisterData
  - _Requisitos: 3.1-3.5_

- [x] 4.2 Criar Repository de Auth
  - Criar Domain/Auth/Repositories/UserRepository
  - Implementar métodos findByEmail, findById, create
  - Implementar conversão Model → DTO
  - _Requisitos: 3.1-3.5_

- [x] 4.3 Criar Actions de Auth
  - Criar Domain/Auth/Actions/LoginUserAction
  - Criar Domain/Auth/Actions/RegisterUserAction
  - Criar Domain/Auth/Actions/LogoutUserAction
  - _Requisitos: 3.1-3.5_

- [x] 4.4 Criar Controllers e Requests de Auth
  - Criar Http/Controllers/Auth/AuthController
  - Criar Http/Requests/Auth/LoginRequest
  - Criar Http/Requests/Auth/RegisterRequest
  - Criar Http/Resources/Auth/UserResource
  - _Requisitos: 3.1-3.5_

- [x] 4.5 Configurar rotas de autenticação
  - Criar routes/api.php se não existir
  - Adicionar rotas públicas: POST /api/login, POST /api/register
  - Adicionar rotas protegidas com auth:sanctum: POST /api/logout, GET /api/me
  - Configurar prefixo /api para todas as rotas da API
  - _Requisitos: 3.1-3.5_


- [x] 5. Implementar feature Career
  - _Requisitos: 12.1_

- [x] 5.1 Criar DTOs de Career
  - Criar Domain/Career/DTOs/CareerData
  - Criar Domain/Career/DTOs/NoticeData
  - _Requisitos: 12.1_

- [x] 5.2 Criar Repositories de Career
  - Criar Domain/Career/Repositories/CareerRepository
  - Criar Domain/Career/Repositories/NoticeRepository
  - _Requisitos: 12.1_

- [x] 5.3 Criar Actions de Career
  - Criar Domain/Career/Actions/ListCareersAction
  - Criar Domain/Career/Actions/GetCareerDetailsAction
  - Criar Domain/Career/Actions/ListExamsByCareerAction
  - _Requisitos: 12.1_

- [x] 5.4 Criar Controllers e Resources de Career
  - Criar Http/Controllers/Api/Career/CareerController
  - Criar Http/Resources/Career/CareerResource
  - _Requisitos: 12.1_

- [x] 5.5 Configurar rotas de Career
  - Adicionar GET /careers
  - Adicionar GET /careers/{id}
  - Adicionar GET /careers/{id}/exams
  - _Requisitos: 12.1_

- [x] 6. Implementar feature Exam
  - _Requisitos: 12.2, 12.3_

- [x] 6.1 Criar DTOs de Exam
  - Criar Domain/Exam/DTOs/ExamData
  - Criar Domain/Exam/DTOs/QuestionData
  - Criar Domain/Exam/DTOs/AttemptData
  - Criar Domain/Exam/DTOs/AnswerData
  - Criar Domain/Exam/DTOs/ResultData
  - _Requisitos: 12.2, 12.3_

- [x] 6.2 Criar Repositories de Exam
  - Criar Domain/Exam/Repositories/ExamRepository
  - Criar Domain/Exam/Repositories/QuestionRepository
  - Criar Domain/Exam/Repositories/AttemptRepository
  - Criar Domain/Exam/Repositories/AnswerRepository
  - Criar Domain/Exam/Repositories/ResultRepository
  - _Requisitos: 12.2, 12.3_

- [x] 6.3 Criar Actions de Exam - Listagem
  - Criar Domain/Exam/Actions/ListExamsAction
  - Criar Domain/Exam/Actions/GetExamDetailsAction
  - _Requisitos: 12.2_

- [x] 6.4 Criar Actions de Exam - Execução
  - Criar Domain/Exam/Actions/StartAttemptAction
  - Criar Domain/Exam/Actions/SubmitAnswerAction
  - Criar Domain/Exam/Actions/FinishAttemptAction
  - Criar Domain/Exam/Actions/CalculateResultAction
  - _Requisitos: 12.3_

- [x] 6.5 Criar Controllers de Exam
  - Criar Http/Controllers/Api/Exam/ExamController
  - Criar Http/Controllers/Api/Exam/AttemptController
  - Criar Http/Requests/Exam/StartAttemptRequest
  - Criar Http/Requests/Exam/SubmitAnswerRequest
  - _Requisitos: 12.2, 12.3_

- [x] 6.6 Criar Resources de Exam
  - Criar Http/Resources/Exam/ExamResource
  - Criar Http/Resources/Exam/QuestionResource
  - Criar Http/Resources/Exam/AttemptResource
  - _Requisitos: 12.2, 12.3_

- [x] 6.7 Configurar rotas de Exam
  - Adicionar GET /exams e GET /exams/{id}
  - Adicionar POST /exams/{id}/start
  - Adicionar GET /attempts/{id}
  - Adicionar POST /attempts/{id}/answer
  - Adicionar POST /attempts/{id}/finish
  - _Requisitos: 12.2, 12.3_

- [x] 7. Implementar feature Ranking
  - _Requisitos: 12.4_

- [x] 7.1 Criar DTOs de Ranking
  - Criar Domain/Ranking/DTOs/RankingData
  - Criar Domain/Ranking/DTOs/RankingEntryData
  - _Requisitos: 12.4_

- [x] 7.2 Criar Repositories de Ranking
  - Criar Domain/Ranking/Repositories/RankingRepository
  - Criar Domain/Ranking/Repositories/ResultRepository
  - _Requisitos: 12.4_

- [x] 7.3 Criar Actions de Ranking
  - Criar Domain/Ranking/Actions/GetRankingAction
  - Criar Domain/Ranking/Actions/GetUserPositionAction
  - Criar Domain/Ranking/Actions/UpdateRankingAction
  - _Requisitos: 12.4_

- [x] 7.4 Criar Controllers e Resources de Ranking
  - Criar Http/Controllers/Api/Ranking/RankingController
  - Criar Http/Resources/Ranking/RankingResource
  - _Requisitos: 12.4_

- [x] 7.5 Configurar rotas de Ranking
  - Adicionar GET /ranking
  - Adicionar GET /ranking/my-position
  - _Requisitos: 12.4_

- [x] 8. Implementar feature Performance
  - _Requisitos: 12.5_

- [x] 8.1 Criar DTOs de Performance
  - Criar Domain/Performance/DTOs/StatisticsData
  - Criar Domain/Performance/DTOs/HistoryData
  - _Requisitos: 12.5_

- [x] 8.2 Criar Repository de Performance
  - Criar Domain/Performance/Repositories/PerformanceRepository
  - _Requisitos: 12.5_

- [x] 8.3 Criar Actions de Performance
  - Criar Domain/Performance/Actions/GetStatisticsAction
  - Criar Domain/Performance/Actions/GetHistoryAction
  - _Requisitos: 12.5_

- [x] 8.4 Criar Controller de Performance
  - Criar Http/Controllers/Api/Performance/PerformanceController
  - _Requisitos: 12.5_

- [x] 8.5 Configurar rotas de Performance
  - Adicionar GET /performance/statistics
  - Adicionar GET /performance/history
  - _Requisitos: 12.5_

- [x] 9. Implementar feature Approved
  - _Requisitos: 12.6_

- [x] 9.1 Criar DTOs de Approved
  - Criar Domain/Approved/DTOs/ApprovedData
  - _Requisitos: 12.6_

- [x] 9.2 Criar Repository de Approved
  - Criar Domain/Approved/Repositories/ApprovedRepository
  - _Requisitos: 12.6_

- [x] 9.3 Criar Action de Approved
  - Criar Domain/Approved/Actions/ListApprovedAction
  - _Requisitos: 12.6_

- [x] 9.4 Criar Controller de Approved
  - Criar Http/Controllers/Api/Approved/ApprovedController
  - _Requisitos: 12.6_

- [x] 9.5 Configurar rotas de Approved
  - Adicionar GET /approved
  - _Requisitos: 12.6_

- [x] 10. Implementar feature Subscription
  - _Requisitos: 12.7_

- [x] 10.1 Criar DTOs de Subscription
  - Criar Domain/Subscription/DTOs/PlanData
  - Criar Domain/Subscription/DTOs/SubscriptionData
  - Criar Domain/Subscription/Enums/PlanType
  - _Requisitos: 12.7_

- [x] 10.2 Criar Repository de Subscription
  - Criar Domain/Subscription/Repositories/SubscriptionRepository
  - _Requisitos: 12.7_

- [x] 10.3 Criar Actions de Subscription
  - Criar Domain/Subscription/Actions/ListPlansAction
  - Criar Domain/Subscription/Actions/CreateSubscriptionAction
  - Criar Domain/Subscription/Actions/GetSubscriptionStatusAction
  - Criar Domain/Subscription/Actions/CancelSubscriptionAction
  - _Requisitos: 12.7_

- [x] 10.4 Criar Controller de Subscription
  - Criar Http/Controllers/Api/Subscription/SubscriptionController
  - Criar Http/Requests/Subscription/CreateSubscriptionRequest
  - _Requisitos: 12.7_

- [x] 10.5 Configurar rotas de Subscription
  - Adicionar GET /plans
  - Adicionar POST /subscribe
  - Adicionar GET /subscription/status
  - Adicionar POST /subscription/cancel
  - _Requisitos: 12.7_

- [x] 11. Implementar feature Notification
  - _Requisitos: 11.1-11.5_

- [x] 11.1 Criar DTOs de Notification
  - Criar Domain/Notification/DTOs/SubscriptionData
  - Criar Domain/Notification/DTOs/NotificationData
  - _Requisitos: 11.1-11.5_

- [x] 11.2 Criar Repository de Notification
  - Criar Domain/Notification/Repositories/NotificationRepository
  - _Requisitos: 11.1-11.5_

- [x] 11.3 Criar Actions de Notification
  - Criar Domain/Notification/Actions/SubscribeToNotificationsAction
  - Criar Domain/Notification/Actions/UnsubscribeFromNotificationsAction
  - Criar Domain/Notification/Actions/SendNotificationAction
  - _Requisitos: 11.1-11.5_

- [x] 11.4 Criar Controller de Notification
  - Criar Http/Controllers/Api/Notification/NotificationController
  - _Requisitos: 11.1-11.5_

- [x] 11.5 Configurar Web Push
  - Instalar minishlink/web-push via Composer
  - Gerar chaves VAPID
  - Configurar serviço de notificações
  - _Requisitos: 11.1-11.5_

- [x] 11.6 Configurar rotas de Notification
  - Adicionar POST /notifications/subscribe
  - Adicionar POST /notifications/unsubscribe
  - _Requisitos: 11.1-11.5_

- [x] 12. Implementar feature User
  - _Requisitos: 12.8_

- [x] 12.1 Criar Actions de User
  - Criar Domain/Auth/Actions/GetUserProfileAction
  - Criar Domain/Auth/Actions/UpdateUserProfileAction
  - Criar Domain/Auth/Actions/DeleteUserAccountAction
  - _Requisitos: 12.8_

- [x] 12.2 Criar Controller de User
  - Criar Http/Controllers/Api/User/UserController
  - Criar Http/Requests/User/UpdateProfileRequest
  - _Requisitos: 12.8_

- [x] 12.3 Configurar rotas de User
  - Adicionar GET /user/profile
  - Adicionar PUT /user/profile
  - Adicionar DELETE /user/account
  - _Requisitos: 12.8_

- [x] 13. Criar e atualizar Seeders para nova estrutura
  - _Requisitos: 13.1-13.8_

- [x] 13.1 Atualizar UserSeeder existente
  - Adicionar usuários com roles diferentes (admin, consultant, user)
  - Adicionar usuários com subscription_status diferentes (active, inactive, trial)
  - Manter AdminUserSeeder existente
  - _Requisitos: 13.1_

- [x] 13.2 Atualizar CareerSeeder existente
  - Atualizar para usar novos nomes de colunas em inglês
  - Popular careers (PM-SP, Bombeiros-RJ, Exército, Marinha, Aeronáutica)
  - _Requisitos: 13.2_

- [x] 13.3 Criar NoticeSeeder
  - Criar editais associados a careers
  - Incluir datas de prova e informações completas
  - _Requisitos: 13.3_

- [x] 13.4 Criar ExamSeeder
  - Criar 3-5 exams por career
  - Incluir título, descrição, tempo limite
  - _Requisitos: 13.4_

- [x] 13.5 Criar QuestionSeeder
  - Criar 30-60 questions por exam
  - Incluir statement, options (A-E), correct_answer, explanation
  - Suportar imagens opcionais em enunciados e alternativas
  - _Requisitos: 13.5_

- [x] 13.6 Criar AttemptSeeder
  - Criar attempts (tentativas) para múltiplos usuários
  - Criar user_answers para cada attempt
  - Incluir timestamps de início e fim
  - _Requisitos: 13.6_

- [x] 13.7 Criar RankingSeeder
  - Popular rankings com resultados de attempts
  - Incluir rankings diários, semanais e mensais
  - _Requisitos: 13.6_

- [x] 13.8 Criar ApprovedSeeder
  - Popular approved com dados de aprovados em diferentes editais
  - Incluir nome, carreira, nota e avatar opcional
  - _Requisitos: 13.7_

- [x] 13.9 Atualizar DatabaseSeeder
  - Adicionar chamadas para todos os seeders na ordem correta
  - Garantir que dependências sejam respeitadas (ex: Career antes de Exam)
  - _Requisitos: 13.1-13.8_

- [x] 14. Atualizar Controllers Admin existentes
  - Atualizar Admin/CarreiraController para usar novos nomes (Career, name, etc)
  - Atualizar Admin/EditalController para usar novos nomes (Notice, title, etc)
  - Atualizar Admin/SimuladoController para usar novos nomes (Exam, title, etc)
  - Atualizar views Blade admin para refletir mudanças
  - _Requisitos: 12.1-12.8_

- [x] 14.1 Checkpoint Backend - Testar todas as APIs
  - Testar endpoints de autenticação (login, register, logout, me)
  - Testar endpoints de Career (list, show, exams)
  - Testar endpoints de Exam (list, show, start, answer, finish)
  - Testar endpoints de Ranking (list, my-position)
  - Testar endpoints de Performance (statistics, history)
  - Testar endpoints de Approved (list)
  - Testar endpoints de Subscription (plans, subscribe, status, cancel)
  - Testar endpoints de User (profile, update, delete)
  - Verificar autenticação e autorização funcionando
  - Verificar validação de dados funcionando
  - Ensure all tests pass, ask the user if questions arise.


- [x] 15. Configurar Vite para integração com Laravel
  - _Requisitos: 1.1-1.5, 2.1-2.5_

- [x] 15.1 Mover código React para Laravel
  - Copiar alfa-quest/src para laravel/resources/react
  - Copiar alfa-quest/components.json para laravel/
  - Copiar alfa-quest/tailwind.config.ts para laravel/
  - Copiar alfa-quest/tsconfig.json e tsconfig.app.json para laravel/
  - Copiar alfa-quest/index.html para laravel/resources/views/app.blade.php (adaptado)
  - _Requisitos: 1.1, 5.1_

- [x] 15.2 Atualizar vite.config.js existente para TypeScript e React
  - Renomear vite.config.js para vite.config.ts
  - Adicionar @vitejs/plugin-react-swc
  - Configurar input para resources/react/main.tsx e resources/react/index.css
  - Configurar alias @ para resources/react
  - Configurar code splitting para vendors (react, react-dom, react-router-dom)
  - Configurar code splitting para UI components (@radix-ui)
  - _Requisitos: 2.1-2.5, 8.5_

- [x] 15.3 Criar view Blade para SPA
  - Criar resources/views/app.blade.php baseado em alfa-quest/index.html
  - Adicionar @vite(['resources/react/main.tsx', 'resources/react/index.css'])
  - Adicionar meta tags PWA (theme-color, manifest, apple-touch-icon)
  - Adicionar meta CSRF token
  - Adicionar div#root para React
  - _Requisitos: 1.2, 4.1_

- [x] 15.4 Configurar rota web para SPA
  - Adicionar rota catch-all GET /{any} em routes/web.php
  - Configurar para retornar view('app')
  - Garantir que rotas /api/* não sejam capturadas
  - _Requisitos: 6.1, 6.2_

- [x] 15.5 Atualizar package.json do Laravel
  - Adicionar dependências React (@types/react, @types/react-dom, react, react-dom)
  - Adicionar dependências React Router (react-router-dom, @types/react-router-dom)
  - Adicionar @vitejs/plugin-react-swc
  - Adicionar TypeScript e tipos necessários
  - Adicionar Zustand para state management
  - Adicionar dependências shadcn/ui já usadas no alfa-quest
  - Scripts dev e build já existem
  - _Requisitos: 2.1_

- [x] 16. Configurar serviços de API no frontend
  - _Requisitos: 1.3, 7.1-7.5_

- [x] 16.1 Criar serviço base de API
  - Criar resources/react/services/api.ts com Axios
  - Configurar baseURL
  - Configurar interceptors para token
  - Configurar interceptor para erros 401
  - _Requisitos: 1.3, 3.3, 3.4_

- [x] 16.2 Criar serviços de Auth
  - Criar resources/react/services/auth.ts
  - Implementar login, register, logout, getMe
  - _Requisitos: 3.1-3.5_

- [x] 16.3 Criar serviços de Exam
  - Criar resources/react/services/exams.ts
  - Implementar listExams, getExam, startAttempt, submitAnswer, finishAttempt
  - _Requisitos: 7.1, 7.2_

- [x] 16.4 Criar serviços de Career
  - Criar resources/react/services/careers.ts
  - Implementar listCareers, getCareer, getCareerExams
  - _Requisitos: 7.1_

- [x] 16.5 Criar serviços de Ranking
  - Criar resources/react/services/ranking.ts
  - Implementar getRanking, getMyPosition
  - _Requisitos: 7.1_

- [x] 16.6 Criar serviços de Performance
  - Criar resources/react/services/performance.ts
  - Implementar getStatistics, getHistory
  - _Requisitos: 7.1_

- [x] 16.7 Criar serviços de Subscription
  - Criar resources/react/services/subscription.ts
  - Implementar getPlans, subscribe, getStatus, cancel
  - _Requisitos: 7.1_

- [x] 16.8 Criar serviços de Notification
  - Criar resources/react/services/notifications.ts
  - Implementar subscribe, unsubscribe
  - _Requisitos: 11.1, 11.5_

- [x] 17. Atualizar stores do frontend para usar APIs reais
  - _Requisitos: 5.5, 7.1-7.5_

- [x] 17.1 Atualizar authStore
  - Remover dados mock
  - Integrar com services/auth.ts
  - Implementar persistência de token
  - _Requisitos: 3.2, 5.5_

- [x] 17.2 Atualizar simuladosStore (renomear para examsStore)
  - Remover dados mock
  - Integrar com services/exams.ts
  - _Requisitos: 5.5, 7.1_

- [x] 17.3 Remover arquivo mocks/data.ts
  - Deletar resources/react/mocks/data.ts
  - _Requisitos: 5.5_

- [x] 18. Atualizar componentes e páginas do frontend
  - _Requisitos: 5.1-5.5_

- [x] 18.1 Atualizar imports de tipos
  - Atualizar tipos em resources/react/types/index.ts
  - Renomear tipos (Simulado → Exam, Questao → Question, etc)
  - _Requisitos: 5.1_

- [x] 18.2 Atualizar página Login
  - Integrar com authStore atualizado
  - Adicionar tratamento de erros de validação
  - _Requisitos: 3.1, 7.3, 7.5_

- [x] 18.3 Atualizar página Simulados (renomear para Exams)
  - Integrar com examsStore
  - Adicionar loading states
  - Adicionar error states
  - _Requisitos: 5.4, 7.1, 7.4_

- [x] 18.4 Atualizar página ExecutarSimulado (renomear para ExecuteExam)
  - Integrar com API de attempts
  - Implementar submit de respostas
  - Implementar finalização
  - _Requisitos: 5.4, 7.2_

- [x] 18.5 Atualizar página Carreiras (renomear para Careers)
  - Integrar com API de careers
  - _Requisitos: 5.4, 7.1_

- [x] 18.6 Atualizar página Ranking
  - Integrar com API de ranking
  - _Requisitos: 5.4, 7.1_

- [x] 18.7 Atualizar página Desempenho (renomear para Performance)
  - Integrar com API de performance
  - _Requisitos: 5.4, 7.1_

- [x] 18.8 Atualizar página Aprovados (renomear para Approved)
  - Integrar com API de approved
  - _Requisitos: 5.4, 7.1_

- [x] 18.9 Atualizar página Assinar (renomear para Subscribe)
  - Integrar com API de subscription
  - _Requisitos: 5.4, 7.1_

- [x] 18.10 Atualizar página Conta (renomear para Account)
  - Integrar com API de user profile
  - _Requisitos: 5.4, 7.1_

- [x] 19. Configurar PWA
  - _Requisitos: 4.1-4.5, 10.1-10.5_

- [x] 19.1 Criar manifest.json
  - Criar public/manifest.json
  - Configurar name, short_name, icons, theme_color
  - Adicionar ícones em múltiplos tamanhos
  - _Requisitos: 4.1, 4.5_

- [x] 19.2 Gerar ícones PWA
  - Criar ícones 72x72, 96x96, 128x128, 144x144, 152x152, 192x192, 384x384, 512x512
  - Salvar em public/icons/
  - _Requisitos: 4.5_

- [x] 19.3 Adicionar meta tags PWA no Blade
  - Adicionar link para manifest
  - Adicionar meta theme-color
  - Adicionar meta apple-mobile-web-app-capable
  - _Requisitos: 4.1, 4.3, 4.4_

- [x] 19.4 Criar Service Worker para notificações
  - Criar public/sw.js
  - Implementar event listener para push
  - Implementar event listener para notificationclick
  - _Requisitos: 11.3, 11.4_

- [x] 19.5 Registrar Service Worker no frontend
  - Adicionar código de registro em resources/react/main.tsx
  - Verificar se navegador suporta Service Workers
  - Registrar /sw.js quando app carrega
  - Adicionar tratamento de erros para registro
  - _Requisitos: 11.1_

- [x] 20. Configurar Docker para frontend
  - _Requisitos: 9.1-9.5_

- [x] 20.1 Atualizar Dockerfile do Laravel
  - Instalar Node.js e npm no container
  - Adicionar comando npm install após composer install
  - Adicionar comando npm run build para produção
  - Garantir que public/build seja criado
  - Configurar permissões corretas para diretórios
  - _Requisitos: 9.1, 9.3_

- [x] 20.2 Atualizar docker-compose.yml
  - Adicionar volume exclusão para node_modules (evitar sincronização com host)
  - Adicionar serviço simulados-vite para dev server
  - Configurar profile: dev para serviço Vite (apenas desenvolvimento)
  - Expor porta 5173 para Vite HMR
  - Configurar variável NODE_ENV
  - _Requisitos: 9.2, 9.4_

- [x] 20.3 Criar scripts de desenvolvimento
  - Documentar comando para rodar Vite dev server: docker-compose --profile dev up simulados-vite
  - Documentar comandos de build e deploy no README
  - Adicionar instruções para desenvolvimento local
  - _Requisitos: 9.2, 9.5_

- [x] 21. Otimizações de produção
  - _Requisitos: 8.1-8.5_

- [x] 21.1 Configurar minificação e cache busting
  - Verificar configuração de minificação no vite.config.ts (já configurado por padrão)
  - Verificar que Vite gera hashes nos nomes de arquivo para cache busting
  - Testar build de produção: npm run build
  - _Requisitos: 8.1, 8.2_

- [x] 21.2 Implementar lazy loading de rotas
  - Atualizar App.tsx para usar React.lazy nas rotas
  - Importar lazy e Suspense do React
  - Criar componente PageLoader para fallback
  - Aplicar lazy loading em todas as páginas principais
  - _Requisitos: 8.5_

- [x] 21.3 Configurar compressão Gzip no Nginx
  - Atualizar nginx/nginx.conf com configurações gzip
  - Adicionar gzip on, gzip_vary, gzip_min_length
  - Configurar gzip_types para JS, CSS, JSON, XML
  - Testar compressão em produção
  - _Requisitos: 8.1_

- [x] 22. Configurar segurança e finalizar integração
  - _Requisitos: 3.1-3.5, 6.4, 1.3_

- [x] 22.1 Publicar e configurar CORS
  - Publicar arquivo de configuração CORS: docker exec -it simulados-app php artisan config:publish cors
  - Editar config/cors.php com as seguintes configurações:
    - paths: ['api/*', 'sanctum/csrf-cookie']
    - allowed_origins: [env('FRONTEND_URL', 'http://localhost:8090'), 'http://localhost:5173']
    - supports_credentials: true (necessário para Sanctum)
    - allowed_methods: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS']
    - allowed_headers: ['Content-Type', 'Authorization', 'X-Requested-With', 'X-CSRF-TOKEN', 'Accept']
    - exposed_headers: []
    - max_age: 0
  - Adicionar FRONTEND_URL=http://localhost:8090 ao .env
  - Reiniciar container: docker-compose restart simulados-app
  - Testar requisições cross-origin usando Postman ou curl:
    - POST http://localhost:8090/api/login com credenciais válidas
    - Verificar header Access-Control-Allow-Origin na resposta
    - Verificar header Access-Control-Allow-Credentials: true
  - Testar no navegador: abrir DevTools → Network → fazer login → verificar headers CORS
  - _Requisitos: 1.3_

- [x] 22.2 Configurar Rate Limiting
  - Editar bootstrap/app.php para adicionar rate limiting no withMiddleware:
    ```php
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo('/admin/login');
        $middleware->throttleApi('60,1'); // 60 req/min
    })
    ```
  - Editar app/Providers/AppServiceProvider.php para adicionar rate limiter customizado no método boot:
    ```php
    use Illuminate\Cache\RateLimiting\Limit;
    use Illuminate\Support\Facades\RateLimiter;
    
    RateLimiter::for('login', function (Request $request) {
        return Limit::perMinute(5)->by($request->ip());
    });
    ```
  - Editar routes/api/auth.php para aplicar throttle:login nas rotas de login:
    ```php
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
    ```
  - Testar rate limiting fazendo múltiplas requisições:
    - Fazer 6+ requisições de login em 1 minuto
    - Verificar que a 6ª retorna 429 Too Many Requests
    - Verificar headers X-RateLimit-Limit e X-RateLimit-Remaining
  - _Requisitos: 3.1_

- [x] 22.3 Configurar CSRF token no Axios
  - Verificar meta CSRF no Blade (já adicionado na tarefa 15.3)
  - Verificar resources/react/services/api.ts já inclui X-CSRF-TOKEN header
  - Confirmar que token é lido do meta tag e adicionado em todas as requisições
  - _Requisitos: 7.2_

- [x] 22.4 Atualizar configuração Laravel Sanctum
  - Editar config/sanctum.php para atualizar stateful domains:
    ```php
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 
        'localhost,localhost:8090,localhost:5173,127.0.0.1,127.0.0.1:8000,::1'
    )),
    ```
  - Adicionar ao .env:
    ```
    SANCTUM_STATEFUL_DOMAINS=localhost:8090,localhost:5173
    SESSION_DOMAIN=localhost
    ```
  - Reiniciar container: docker-compose restart simulados-app
  - Testar autenticação completa:
    - Abrir http://localhost:8090 no navegador
    - Fazer login com credenciais válidas (admin@alfa.com / admin123)
    - Verificar que token é salvo no localStorage
    - Navegar para página protegida (ex: /simulados)
    - Verificar que requisição inclui header Authorization
    - Fazer logout e verificar redirecionamento
  - _Requisitos: 3.1-3.5_

- [x] 23. Checkpoint Final - Testar aplicação completa end-to-end
  - **Fluxo de Autenticação:**
    - Abrir http://localhost:8090/login
    - Tentar login com credenciais inválidas → verificar mensagem de erro
    - Fazer login com admin@alfa.com / admin123 → verificar redirecionamento para home
    - Verificar que token está salvo no localStorage (DevTools → Application → Local Storage)
    - Tentar acessar rota protegida sem token → verificar redirecionamento para login
    - Fazer logout → verificar que token é removido e redireciona para login
  - **Navegação entre Páginas:**
    - Testar todas as rotas principais: /, /carreiras, /simulados, /ranking, /desempenho, /aprovados, /assinar, /conta
    - Verificar que navegação é client-side (sem reload completo da página)
    - Atualizar página (F5) em qualquer rota → verificar que rota é mantida
    - Verificar que 404 aparece para rotas inexistentes
  - **Execução de Simulado:**
    - Navegar para /simulados
    - Selecionar um simulado e clicar em "Iniciar"
    - Verificar que cronômetro inicia
    - Responder algumas questões
    - Finalizar simulado
    - Verificar que resultado é exibido com pontuação correta
  - **Ranking e Desempenho:**
    - Navegar para /ranking → verificar que lista de usuários carrega
    - Verificar posição do usuário logado
    - Navegar para /desempenho → verificar estatísticas e gráficos
  - **Sistema de Assinaturas:**
    - Fazer login com usuário free (maria@example.com / senha123)
    - Tentar acessar simulado premium → verificar paywall
    - Navegar para /assinar → verificar planos disponíveis
    - Fazer login com usuário premium (joao@example.com / senha123)
    - Verificar acesso a simulados premium
  - **Responsividade:**
    - Abrir DevTools → Toggle device toolbar (Ctrl+Shift+M)
    - Testar em iPhone SE (375px) → verificar menu mobile, layout adaptado
    - Testar em iPad (768px) → verificar layout tablet
    - Testar em Desktop (1920px) → verificar uso completo do espaço
  - **PWA:**
    - Abrir DevTools → Application → Service Workers → verificar que sw.js está registrado e ativo
    - Application → Manifest → verificar que manifest.json carrega corretamente
    - Verificar que ícones estão acessíveis (clicar em cada ícone no manifest)
    - Em dispositivo móvel ou Chrome desktop: verificar se aparece prompt "Instalar app"
  - **Notificações Push:**
    - Navegar para /conta ou página que solicita permissão de notificações
    - Clicar em "Permitir notificações" → verificar prompt do navegador
    - Aceitar permissão → verificar que subscription é salva (verificar no console ou DevTools)
    - Testar envio manual de notificação via backend (opcional)
  - **Validação de APIs:**
    - Abrir DevTools → Network → filtrar por "api"
    - Navegar pela aplicação e verificar que todas as requisições retornam 200 ou códigos apropriados
    - Verificar estrutura JSON das respostas (data, meta, etc)
    - Verificar que erros retornam mensagens apropriadas (422 para validação, 401 para auth, etc)
  - Se encontrar problemas, documentar e criar issues antes de prosseguir
  - _Requisitos: 3.1-3.5, 10.1-10.5, 11.1-11.5, 12.1-12.8_

- [x] 24. Build e validação de produção
  - _Requisitos: 1.1, 2.3, 8.1-8.5_

- [x] 24.1 Executar build de produção
  - Executar npm run build no diretório laravel/
  - Verificar arquivos gerados em public/build
  - Verificar manifest.json está acessível
  - Verificar que assets têm hashes para cache busting
  - Verificar tamanho dos bundles gerados
  - _Requisitos: 2.1, 2.3, 8.2_

- [x] 24.2 Validar PWA com Lighthouse
  - Abrir aplicação em Chrome DevTools (F12)
  - Navegar para aba Lighthouse
  - Executar Lighthouse audit em modo Desktop e Mobile
  - Verificar pontuação >90 em Performance:
    - First Contentful Paint < 1.8s
    - Largest Contentful Paint < 2.5s
    - Total Blocking Time < 200ms
    - Cumulative Layout Shift < 0.1
  - Verificar pontuação >90 em Accessibility:
    - Contraste de cores adequado
    - Labels em formulários
    - Alt text em imagens
  - Verificar pontuação >90 em Best Practices:
    - HTTPS (em produção)
    - Sem erros no console
    - Bibliotecas atualizadas
  - Verificar pontuação >90 em SEO:
    - Meta tags presentes
    - Viewport configurado
    - Títulos de página
  - Verificar pontuação >90 em PWA:
    - Manifest válido
    - Service Worker registrado
    - Ícones em múltiplos tamanhos
    - Theme color configurado
  - Documentar problemas encontrados e criar issues se necessário
  - Corrigir problemas críticos identificados
  - _Requisitos: 4.1-4.5, 8.4, 10.1-10.5_

- [ ]* 24.3 Escrever testes de integração para APIs críticas
  - Criar testes para fluxo de autenticação (tests/Feature/Auth/):
    - Teste de login com credenciais válidas (retorna token e user)
    - Teste de login com credenciais inválidas (retorna 401)
    - Teste de registro de novo usuário (cria user e retorna token)
    - Teste de acesso a rota protegida sem token (retorna 401)
    - Teste de acesso a rota protegida com token válido (retorna 200)
    - Teste de logout (invalida token)
  - Criar testes para fluxo de simulado (tests/Feature/Exam/):
    - Teste de listagem de simulados (retorna array de exams)
    - Teste de iniciar simulado (POST /api/exams/{id}/start cria attempt)
    - Teste de submeter resposta (POST /api/attempts/{id}/answer salva answer)
    - Teste de finalizar simulado (POST /api/attempts/{id}/finish calcula resultado)
    - Teste de cálculo de resultado correto (score = correct/total * 100)
  - Criar testes para validação de propriedades (tests/Feature/Properties/):
    - **Propriedade 1**: Todas as rotas /api/* retornam JSON válido com Content-Type correto
    - **Propriedade 3**: Requisições autenticadas incluem token no header Authorization
    - **Propriedade 4**: Rotas protegidas retornam 401 sem token válido
    - **Propriedade 6**: Operações de mutação usam métodos HTTP corretos (POST/PUT/DELETE)
  - Executar testes: php artisan test ou docker exec -it simulados-app php artisan test
  - Verificar que todos os testes passam
  - Verificar cobertura de código (opcional): php artisan test --coverage
  - Documentar como executar testes no README (já documentado)
  - _Requisitos: 3.1-3.5, 7.1-7.5, 12.2-12.3_

- [ ] 24.5 Testar instalação PWA em dispositivos reais
  - Testar instalação do PWA em dispositivo móvel Android:
    - Abrir no Chrome
    - Verificar banner "Adicionar à tela inicial"
    - Instalar e verificar ícone na tela inicial
    - Abrir app instalado e verificar modo standalone
  - Testar instalação do PWA em dispositivo móvel iOS:
    - Abrir no Safari
    - Usar "Adicionar à Tela de Início"
    - Verificar ícone na tela inicial
    - Abrir app e verificar comportamento
  - Testar instalação no Desktop (Chrome/Edge):
    - Verificar ícone de instalação na barra de endereço
    - Instalar PWA
    - Abrir como app standalone
  - Verificar ícones estão carregando corretamente em todos os tamanhos
  - Verificar splash screen aparece ao abrir (Android)
  - Verificar app abre em modo standalone (sem barra do navegador)
  - Testar notificações push em dispositivo real:
    - Solicitar permissão
    - Enviar notificação de teste do backend
    - Verificar notificação aparece
    - Clicar na notificação e verificar navegação
  - Documentar comportamento em cada plataforma
  - _Requisitos: 4.1-4.5, 11.1-11.5_

- [x] 24.4 Documentação final
  - Criar/atualizar README.md com instruções de setup
  - Documentar comandos de build: npm install, npm run build
  - Documentar comandos Docker: docker-compose up, docker-compose --profile dev up
  - Documentar variáveis de ambiente necessárias (.env)
  - Documentar processo de geração de chaves VAPID para notificações
  - Documentar estrutura de diretórios do projeto
  - Adicionar troubleshooting comum
  - _Requisitos: 1.1-1.5, 9.5_
