# Requirements Document

## Introduction

Este documento especifica as melhorias no sistema de autenticação e acesso do PWA Operação Alfa, implementando um modelo freemium onde usuários podem se cadastrar gratuitamente e ter acesso a simulados selecionados, enquanto simulados premium ficam bloqueados. Também inclui ajustes visuais no logo e adição do logo no painel administrativo.

## Glossary

- **PWA**: Progressive Web App - aplicação React do frontend
- **Admin Panel**: Painel administrativo Laravel usando AdminLTE
- **Free User**: Usuário com conta gratuita, acesso limitado a simulados marcados como gratuitos
- **Premium User**: Usuário assinante com acesso completo a todos os simulados
- **Exam**: Simulado/prova disponível no sistema
- **Freemium Model**: Modelo de negócio onde funcionalidades básicas são gratuitas e avançadas são pagas

## Requirements

### Requirement 1

**User Story:** Como visitante, quero me cadastrar gratuitamente no sistema, para que eu possa acessar simulados gratuitos sem precisar pagar.

#### Acceptance Criteria

1. WHEN a user visits the login page THEN the PWA SHALL display a "Criar conta gratuita" button instead of "Assinar agora"
2. WHEN a user clicks "Criar conta gratuita" THEN the PWA SHALL navigate to a registration page
3. WHEN a user submits valid registration data (name, email, password) THEN the PWA SHALL create a free account and log the user in
4. WHEN a user submits invalid registration data THEN the PWA SHALL display appropriate validation error messages
5. WHEN the login page loads THEN the PWA SHALL NOT display test credentials section

### Requirement 2

**User Story:** Como administrador, quero configurar quais simulados são gratuitos ou premium, para que eu possa controlar o acesso dos usuários.

#### Acceptance Criteria

1. WHEN an admin views the exam list THEN the Admin Panel SHALL display a column indicating if each exam is free or premium
2. WHEN an admin creates or edits an exam THEN the Admin Panel SHALL provide a toggle/checkbox to mark the exam as "free" or "premium"
3. WHEN an exam is marked as free THEN the Exam model SHALL store this configuration in the database
4. WHEN the system queries exams THEN the API SHALL include the free/premium status in the response

### Requirement 3

**User Story:** Como usuário free, quero ver quais simulados estão disponíveis para mim e quais estão bloqueados, para que eu saiba o que posso acessar e como desbloquear.

#### Acceptance Criteria

1. WHEN a free user views the exam list THEN the PWA SHALL display all exams with visual distinction between free (accessible) and premium (locked)
2. WHEN a free user views a premium exam card THEN the PWA SHALL display a lock indicator and a "Liberar acesso" button
3. WHEN a free user clicks "Liberar acesso" on a premium exam THEN the PWA SHALL navigate to the subscription page
4. WHEN a free user attempts to directly access a premium exam URL THEN the PWA SHALL display a paywall modal with subscription option
5. WHEN a free user accesses a free exam THEN the PWA SHALL allow full access to the exam without restrictions
6. WHEN a premium user views the exam list THEN the PWA SHALL display all exams as accessible without lock indicators

### Requirement 4

**User Story:** Como usuário, quero ver o logo em tamanho adequado na tela de login, para que a identidade visual seja preservada.

#### Acceptance Criteria

1. WHEN the login page loads THEN the logo SHALL have max-width of 100% and height of 300px
2. WHEN the logo is displayed THEN the logo SHALL NOT have fixed width and height classes that override responsive behavior
3. WHEN the logo is displayed on different screen sizes THEN the logo SHALL scale proportionally within the max-width constraint

### Requirement 5

**User Story:** Como administrador, quero ver o logo da Operação Alfa no painel administrativo, para que a identidade visual seja consistente.

#### Acceptance Criteria

1. WHEN an admin accesses the Admin Panel THEN the sidebar SHALL display the Operação Alfa logo
2. WHEN an admin views the login page THEN the Admin Panel login SHALL display the Operação Alfa logo
3. WHEN the logo is displayed in the Admin Panel THEN the logo SHALL be properly sized for the sidebar context
