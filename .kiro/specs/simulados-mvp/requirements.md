# Requirements Document

## Introduction

Este documento especifica os requisitos para a implementação completa da funcionalidade de Simulados no sistema Operação Alfa. O sistema permite que administradores criem e gerenciem simulados com questões de múltipla escolha, incluindo suporte a imagens complementares. Os usuários do PWA podem realizar esses simulados, respondendo questões e recebendo feedback sobre seu desempenho.

O sistema já possui a estrutura básica de simulados (exams) implementada, mas falta a funcionalidade de gerenciamento de questões no painel administrativo e a integração completa no PWA para exibição e resposta das questões.

## Glossary

- **Exam (Simulado)**: Conjunto de questões de múltipla escolha associado a uma carreira militar
- **Question (Questão)**: Item de avaliação com enunciado, 5 alternativas (A-E), resposta correta e explicação opcional
- **Attempt (Tentativa)**: Registro de uma execução de simulado por um usuário
- **UserAnswer (Resposta do Usuário)**: Registro da resposta escolhida pelo usuário para uma questão
- **Statement (Enunciado)**: Texto da pergunta que pode incluir uma imagem complementar
- **Option (Alternativa)**: Uma das 5 opções de resposta (A, B, C, D, E) que pode incluir texto e/ou imagem
- **Admin Panel**: Painel administrativo Laravel/Blade para gerenciamento do sistema
- **PWA**: Progressive Web App React para usuários finais
- **Feedback Mode (Modo de Feedback)**: Configuração que define quando o usuário recebe feedback sobre suas respostas - imediato (ao responder cada questão) ou final (ao concluir o simulado)
- **Time Limit (Tempo Limite)**: Duração máxima em minutos para completar um simulado
- **Ranking**: Classificação de usuários baseada em seus desempenhos nos simulados

## Requirements

### Requirement 1

**User Story:** As an administrator, I want to manage questions for each exam, so that I can create complete exams with all necessary content.

#### Acceptance Criteria

1. WHEN an administrator accesses an exam's edit page THEN the Admin_Panel SHALL display a list of all questions associated with that exam ordered by question number
2. WHEN an administrator clicks "Add Question" THEN the Admin_Panel SHALL display a form with fields for question number, statement, statement image, five options (A-E) with text and optional images, correct answer, and explanation
3. WHEN an administrator submits a valid question form THEN the Admin_Panel SHALL create the question and associate it with the exam
4. WHEN an administrator edits an existing question THEN the Admin_Panel SHALL load all current values and allow modification of any field
5. WHEN an administrator deletes a question THEN the Admin_Panel SHALL remove the question and update the question count for the exam
6. WHEN an administrator uploads an image for statement or options THEN the Admin_Panel SHALL store the image and display a preview

### Requirement 2

**User Story:** As an administrator, I want to configure exam settings including feedback mode, so that I can customize the exam experience for users.

#### Acceptance Criteria

1. WHEN an administrator creates or edits an exam THEN the Admin_Panel SHALL display a feedback mode selector with options "immediate" (ao responder) and "final" (ao finalizar)
2. WHEN an administrator sets feedback mode to "immediate" THEN the Exam SHALL be configured to show correct answer and explanation after each question is answered
3. WHEN an administrator sets feedback mode to "final" THEN the Exam SHALL be configured to show all results only after the exam is completed
4. WHEN an administrator sets a time limit THEN the Admin_Panel SHALL validate that the value is between 1 and 300 minutes

### Requirement 3

**User Story:** As an administrator, I want to validate question data before saving, so that all exams have consistent and complete content.

#### Acceptance Criteria

1. WHEN an administrator submits a question without a statement THEN the Admin_Panel SHALL display a validation error and prevent submission
2. WHEN an administrator submits a question without all five option texts THEN the Admin_Panel SHALL display a validation error and prevent submission
3. WHEN an administrator submits a question without selecting a correct answer THEN the Admin_Panel SHALL display a validation error and prevent submission
4. WHEN an administrator submits a question with a duplicate question number THEN the Admin_Panel SHALL display a validation error and prevent submission
5. WHEN an administrator uploads an image larger than 2MB THEN the Admin_Panel SHALL display a validation error and prevent upload

### Requirement 4

**User Story:** As a user, I want to view and answer exam questions in the PWA with a visible timer, so that I can practice for military career exams under realistic conditions.

#### Acceptance Criteria

1. WHEN a user starts an exam attempt THEN the PWA SHALL load all questions for that exam from the API and start a countdown timer based on the exam's time limit
2. WHEN a user views a question THEN the PWA SHALL display the statement, statement image (if present), and all five options with their respective images (if present)
3. WHEN a user selects an answer option THEN the PWA SHALL visually highlight the selected option and enable navigation to the next question
4. WHEN a user submits an answer THEN the PWA SHALL send the answer to the API and store it for the current attempt
5. WHEN a user navigates between questions THEN the PWA SHALL preserve previously selected answers and display them correctly
6. WHILE an exam is in progress THEN the PWA SHALL display a countdown timer showing remaining time in MM:SS or HH:MM:SS format
7. WHEN the timer reaches zero THEN the PWA SHALL automatically finish the attempt and display results
8. WHEN the timer has less than 5 minutes remaining THEN the PWA SHALL display the timer in red color to alert the user

### Requirement 5

**User Story:** As a user, I want to receive feedback on my answers based on the exam's feedback mode, so that I can learn effectively.

#### Acceptance Criteria

1. WHEN feedback mode is "immediate" and a user submits an answer THEN the PWA SHALL immediately display whether the answer is correct or incorrect, highlight the correct answer, and show the explanation
2. WHEN feedback mode is "immediate" and a user answers incorrectly THEN the PWA SHALL highlight the user's answer in red and the correct answer in green
3. WHEN feedback mode is "final" and a user submits an answer THEN the PWA SHALL only confirm the answer was recorded without revealing correctness
4. WHEN feedback mode is "final" and a user finishes the exam THEN the PWA SHALL display all results including correct/incorrect status for each question

### Requirement 6

**User Story:** As a user, I want to see my exam results with explanations, so that I can learn from my mistakes.

#### Acceptance Criteria

1. WHEN a user finishes an exam THEN the PWA SHALL display the total score, number of correct answers, total questions, and time spent
2. WHEN a user reviews a completed exam THEN the PWA SHALL show each question with the user's answer, correct answer, and explanation (if available)
3. WHEN displaying results THEN the PWA SHALL visually distinguish correct answers (green) from incorrect answers (red)
4. WHEN a user finishes an exam THEN the System SHALL update the user's ranking position based on the score achieved

### Requirement 7

**User Story:** As a developer, I want the API to properly serialize question data, so that the PWA can display questions correctly.

#### Acceptance Criteria

1. WHEN the API returns exam details THEN the API SHALL include all questions with their complete data (statement, images, options) and the exam's feedback mode and time limit
2. WHEN the API returns questions for an ongoing attempt with "final" feedback mode THEN the API SHALL exclude the correct answer and explanation fields
3. WHEN the API returns questions for an ongoing attempt with "immediate" feedback mode THEN the API SHALL include correct answer and explanation only for already answered questions
4. WHEN the API returns questions for a finished attempt THEN the API SHALL include the correct answer and explanation fields for all questions
5. WHEN the API returns image URLs THEN the API SHALL return complete accessible URLs for all images

### Requirement 8

**User Story:** As a system, I want to integrate exam results with the ranking system, so that users can compete and track their progress.

#### Acceptance Criteria

1. WHEN a user finishes an exam THEN the System SHALL calculate the final score as a percentage of correct answers
2. WHEN a user finishes an exam THEN the System SHALL create or update a ranking entry with the user's score and time
3. WHEN calculating ranking position THEN the System SHALL order users by score (descending) and then by time (ascending) for tie-breaking
4. WHEN a user completes multiple attempts of the same exam THEN the System SHALL use the best score for ranking purposes

