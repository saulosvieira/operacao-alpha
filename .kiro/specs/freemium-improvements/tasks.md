# Implementation Plan

## 1. Database and Model Updates

- [x] 1. Database and Model Updates
  - [x] 1.1 Create migration to add `is_free` column to exams table
    - Create new migration file: `laravel/database/migrations/YYYY_MM_DD_HHMMSS_add_is_free_to_exams_table.php`
    - Add boolean column `is_free` with default `false` after `active` column
    - Run migration with `php artisan migrate`
    - _Requirements: 2.3_
  - [x] 1.2 Update Exam model with `is_free` field
    - Add `is_free` to `$fillable` array in `laravel/app/Domain/Exam/Models/Exam.php`
    - Add `is_free` to `$casts` array as boolean
    - _Requirements: 2.3, 2.4_
  - [ ]* 1.3 Write property test for is_free persistence
    - **Property 3: Exam is_free persistence**
    - **Validates: Requirements 2.3**

## 2. Backend API - Exam Resource Updates

- [x] 2. Backend API - Exam Resource Updates
  - [x] 2.1 Update Exam API Resource to include is_free field
    - Locate and update the Exam resource in `laravel/app/Http/Resources/Exam/`
    - Add `'isFree' => $this->is_free` to the resource array
    - Ensure API responses include the is_free field in camelCase format
    - _Requirements: 2.4_

## 3. Admin Panel - Exam Management

- [x] 3. Admin Panel - Exam Management
  - [x] 3.1 Update exam list view to show free/premium status
    - Add "Tipo" column in `laravel/resources/views/admin/exams/index.blade.php` after Status column
    - Show "Gratuito" badge (badge-success) when `$exam->is_free` is true
    - Show "Premium" badge (badge-warning) when `$exam->is_free` is false
    - _Requirements: 2.1_
  - [x] 3.2 Update exam create form with is_free toggle
    - Add checkbox/toggle for "Simulado Gratuito" in `laravel/resources/views/admin/exams/create.blade.php`
    - Place after the "Active Exam" toggle
    - Use custom-control custom-switch pattern for consistency
    - _Requirements: 2.2_
  - [x] 3.3 Update exam edit form with is_free toggle
    - Add checkbox/toggle for "Simulado Gratuito" in `laravel/resources/views/admin/exams/edit.blade.php`
    - Pre-populate with current `$exam->is_free` value
    - Place after the "Active Exam" toggle
    - _Requirements: 2.2_
  - [x] 3.4 Update exam store/update controller methods
    - Update the store and update methods in the exam controller to handle `is_free` field
    - Add validation rule for `is_free` as boolean
    - _Requirements: 2.2, 2.3_

## 4. Admin Panel - Logo Integration

- [x] 4. Admin Panel - Logo Integration
  - [x] 4.1 Copy logo to public folder and configure AdminLTE
    - Copy `logo-operacao-alfa.png` from `laravel/resources/react/assets/` to `laravel/public/images/`
    - Update `laravel/config/adminlte.php`:
      - Set `logo_img` to `'images/logo-operacao-alfa.png'`
      - Set `auth_logo.enabled` to `true`
      - Set `auth_logo.img.path` to `'images/logo-operacao-alfa.png'`
      - Configure appropriate width/height (e.g., 150x150)
    - _Requirements: 5.1, 5.2, 5.3_

## 5. Checkpoint - Backend Complete

- [x] 5. Checkpoint - Ensure all backend changes work
  - Verify migration ran successfully
  - Test admin panel exam creation/editing with is_free toggle
  - Verify API returns is_free field
  - Ensure all tests pass, ask the user if questions arise

## 6. Frontend - Type Updates

- [x] 6. Frontend - Type Updates
  - [x] 6.1 Update Exam type to include isFree
    - Add `isFree: boolean` to Exam interface in `laravel/resources/react/types/index.ts`
    - Place after `active: boolean` field
    - _Requirements: 2.4_
  - [ ]* 6.2 Write property test for API response format
    - **Property 4: API includes is_free in response**
    - **Validates: Requirements 2.4**

## 7. Frontend - Registration Page

- [x] 7. Frontend - Registration Page
  - [x] 7.1 Create registration page component
    - Create `laravel/resources/react/pages/Cadastro.tsx` with form fields:
      - Name (required, non-empty)
      - Email (required, valid email format)
      - Password (required, min 6 characters)
      - Confirm Password (required, must match password)
    - Implement form validation with zod
    - Use existing `register` function from authStore
    - Navigate to `/simulados` on success
    - Show validation errors appropriately
    - Style consistently with Login page (card-tactical, similar layout)
    - _Requirements: 1.2, 1.3, 1.4_
  - [x] 7.2 Add registration route to App.tsx
    - Import Cadastro component (lazy loaded)
    - Add public route `/cadastro` in `laravel/resources/react/App.tsx`
    - Place with other public routes (near /login)
    - _Requirements: 1.2_
  - [ ]* 7.3 Write property tests for registration validation
    - **Property 1: Valid registration creates free account**
    - **Property 2: Invalid registration shows errors**
    - **Validates: Requirements 1.3, 1.4**

## 8. Frontend - Login Page Updates

- [x] 8. Frontend - Login Page Updates
  - [x] 8.1 Update Login page
    - Remove entire "Credenciais de Teste" card section from `laravel/resources/react/pages/Login.tsx`
    - Change "Assinar agora" button text to "Criar conta gratuita"
    - Update button link from `/assinar` to `/cadastro`
    - _Requirements: 1.1, 1.5_
  - [x] 8.2 Fix logo styling on Login page
    - Remove fixed `w-24 h-24` classes from logo img in `laravel/resources/react/pages/Login.tsx`
    - Add inline style: `style={{ maxWidth: '100%', height: '300px' }}`
    - Ensure logo scales responsively within container
    - _Requirements: 4.1, 4.2, 4.3_

## 9. Frontend - Simulados Page Updates

- [x] 9. Frontend - Simulados Page Updates
  - [x] 9.1 Update exam card components for free/premium distinction
    - Update `SimuladoCard` component in `laravel/resources/react/pages/Simulados.tsx`
    - Change access logic: check `exam.isFree` field instead of just subscription status
    - Update `isBlocked` logic: `!exam.isFree && !isSubscribed`
    - Show lock icon only for premium exams when user is free
    - Add "Premium" badge for premium exams (`!exam.isFree`)
    - Change button text to "Liberar acesso" for locked premium exams
    - Navigate to `/assinar` when clicking "Liberar acesso"
    - Keep "Iniciar" button for accessible exams
    - _Requirements: 3.1, 3.2, 3.3_
  - [x] 9.2 Update exam detail page access logic
    - Update `laravel/resources/react/pages/Simulado.tsx`
    - Create `canAccessExam` helper: `exam.isFree || isSubscribed`
    - Update `isBlocked` logic to use `canAccessExam`
    - Change button text to "Liberar acesso" for blocked exams
    - Keep existing PaywallModal behavior (if implemented)
    - _Requirements: 3.4, 3.5, 3.6_
  - [ ]* 9.3 Write property test for access control
    - **Property 5: Free user access control**
    - **Validates: Requirements 3.4, 3.5**

## 10. Final Checkpoint

- [x] 10. Final Checkpoint - Ensure all tests pass
  - Test complete registration flow
  - Test free user can access free exams
  - Test free user is blocked from premium exams
  - Test premium user can access all exams
  - Verify admin panel logo displays correctly
  - Ensure all tests pass, ask the user if questions arise
