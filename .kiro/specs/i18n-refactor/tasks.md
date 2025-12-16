# Implementation Plan

- [ ] 1. Create Admin Actions for Career domain
  - [x] 1.1 Create ListCareersForAdminAction
    - Implement action to list careers with exam counts for admin panel
    - Return paginated CareerData DTOs
    - _Requirements: 3.1.1, 3.1.2, 4.1_
  
  - [x] 1.2 Create GetCareerForEditAction
    - Implement action to get single career with exam count for editing
    - Return CareerData DTO
    - _Requirements: 3.1.1, 3.1.2, 4.1_
  
  - [x] 1.3 Create CreateCareerAction
    - Implement action to create new career with slug generation
    - Accept name, description, active status
    - Return CareerData DTO
    - _Requirements: 3.1.1, 3.1.2, 3.1.4_
  
  - [x] 1.4 Create UpdateCareerAction
    - Implement action to update existing career
    - Accept career ID, name, description, active status
    - Return CareerData DTO
    - _Requirements: 3.1.1, 3.1.2, 3.1.4_
  
  - [x] 1.5 Create DeleteCareerAction
    - Implement action to delete career
    - Validate no exams are associated
    - Throw exception if validation fails
    - _Requirements: 3.1.1, 3.1.4_

- [x] 2. Create Admin Actions for Notice domain
  - [x] 2.1 Create ListNoticesForAdminAction
    - Implement action to list notices with career relationship
    - Return paginated NoticeData DTOs with nested CareerData
    - _Requirements: 3.1.1, 3.1.2, 4.1_
  
  - [x] 2.2 Create GetNoticeForEditAction
    - Implement action to get single notice with career for editing
    - Return NoticeData DTO with nested CareerData
    - _Requirements: 3.1.1, 3.1.2, 4.1_
  
  - [x] 2.3 Create ListActiveCareersAction
    - Implement action to list active careers for dropdowns
    - Return collection of CareerData DTOs
    - _Requirements: 3.1.1, 3.1.2, 4.1_
  
  - [x] 2.4 Create CreateNoticeAction
    - Implement action to create new notice
    - Accept career_id, title, description, exam_date
    - Return NoticeData DTO
    - _Requirements: 3.1.1, 3.1.2, 3.1.4_
  
  - [x] 2.5 Create UpdateNoticeAction
    - Implement action to update existing notice
    - Accept notice ID and update data
    - Return NoticeData DTO
    - _Requirements: 3.1.1, 3.1.2, 3.1.4_
  
  - [x] 2.6 Create DeleteNoticeAction
    - Implement action to delete notice
    - _Requirements: 3.1.1, 3.1.4_

- [x] 3. Create Admin Actions for Exam domain
  - [x] 3.1 Create ListExamsForAdminAction
    - Implement action to list exams with career and question count
    - Return paginated ExamData DTOs with nested CareerData
    - _Requirements: 3.1.1, 3.1.2, 4.1_
  
  - [x] 3.2 Create GetExamForEditAction
    - Implement action to get single exam with career and question count
    - Return ExamData DTO with nested CareerData
    - _Requirements: 3.1.1, 3.1.2, 4.1_
  
  - [x] 3.3 Create CreateExamAction
    - Implement action to create new exam
    - Accept career_id, title, description, time_limit_minutes, active
    - Return ExamData DTO
    - _Requirements: 3.1.1, 3.1.2, 3.1.4_
  
  - [x] 3.4 Create UpdateExamAction
    - Implement action to update existing exam
    - Accept exam ID and update data
    - Return ExamData DTO
    - _Requirements: 3.1.1, 3.1.2, 3.1.4_
  
  - [x] 3.5 Create DeleteExamAction
    - Implement action to delete exam
    - Validate no questions are associated
    - Throw exception if validation fails
    - _Requirements: 3.1.1, 3.1.4_

- [x] 4. Refactor CarreiraController to use Actions and English variables
  - [x] 4.1 Update index method
    - Inject ListCareersForAdminAction
    - Call action to get paginated careers
    - Pass $careers variable (not $carreiras) to view
    - Update view path to 'admin.careers.index'
    - _Requirements: 2.1, 2.3, 3.1.1, 3.1.3, 4.1, 4.2, 4.3, 5.1_
  
  - [x] 4.2 Update create method
    - Update view path to 'admin.careers.create'
    - _Requirements: 5.1_
  
  - [x] 4.3 Update store method
    - Inject CreateCareerAction
    - Call action with validated data
    - Remove direct model access
    - _Requirements: 3.1.1, 3.1.4, 4.1, 4.4_
  
  - [x] 4.4 Update edit method
    - Inject GetCareerForEditAction
    - Call action to get career DTO
    - Pass $career variable (not $carreira) to view
    - Update view path to 'admin.careers.edit'
    - _Requirements: 2.1, 3.1.1, 3.1.3, 4.1, 4.3, 5.1_
  
  - [x] 4.5 Update update method
    - Inject UpdateCareerAction
    - Call action with validated data
    - Remove direct model access
    - _Requirements: 3.1.1, 3.1.4, 4.1, 4.4_
  
  - [x] 4.6 Update destroy method
    - Inject DeleteCareerAction
    - Call action to delete career
    - Handle exceptions for business rule violations
    - _Requirements: 3.1.1, 3.1.4, 4.1_

- [x] 5. Refactor EditalController to use Actions and English variables
  - [x] 5.1 Update index method
    - Inject ListNoticesForAdminAction
    - Call action to get paginated notices
    - Pass $notices variable (not $editais) to view
    - Update view path to 'admin.notices.index'
    - _Requirements: 2.1, 2.3, 3.1.1, 3.1.3, 4.1, 4.2, 4.3, 5.1_
  
  - [x] 5.2 Update create method
    - Inject ListActiveCareersAction
    - Call action to get active careers
    - Pass $careers variable (not $carreiras) to view
    - Update view path to 'admin.notices.create'
    - _Requirements: 2.1, 2.3, 3.1.1, 3.1.3, 4.1, 4.3, 5.1_
  
  - [x] 5.3 Update store method
    - Inject CreateNoticeAction
    - Call action with validated data
    - Remove direct model access
    - _Requirements: 3.1.1, 3.1.4, 4.1, 4.4_
  
  - [x] 5.4 Update edit method
    - Inject GetNoticeForEditAction and ListActiveCareersAction
    - Call actions to get notice and careers DTOs
    - Pass $notice and $careers variables to view
    - Update view path to 'admin.notices.edit'
    - _Requirements: 2.1, 3.1.1, 3.1.3, 4.1, 4.3, 5.1_
  
  - [x] 5.5 Update update method
    - Inject UpdateNoticeAction
    - Call action with validated data
    - Remove direct model access
    - _Requirements: 3.1.1, 3.1.4, 4.1, 4.4_
  
  - [x] 5.6 Update destroy method
    - Inject DeleteNoticeAction
    - Call action to delete notice
    - _Requirements: 3.1.1, 3.1.4, 4.1_

- [x] 6. Refactor SimuladoController to use Actions and English variables
  - [x] 6.1 Update index method
    - Inject ListExamsForAdminAction
    - Call action to get paginated exams
    - Pass $exams variable (not $simulados) to view
    - Update view path to 'admin.exams.index'
    - _Requirements: 2.1, 2.3, 3.1.1, 3.1.3, 4.1, 4.2, 4.3, 5.1_
  
  - [x] 6.2 Update create method
    - Inject ListActiveCareersAction
    - Call action to get active careers
    - Pass $careers variable (not $carreiras) to view
    - Update view path to 'admin.exams.create'
    - _Requirements: 2.1, 2.3, 3.1.1, 3.1.3, 4.1, 4.3, 5.1_
  
  - [x] 6.3 Update store method
    - Inject CreateExamAction
    - Call action with validated data
    - Remove direct model access
    - _Requirements: 3.1.1, 3.1.4, 4.1, 4.4_
  
  - [x] 6.4 Update edit method
    - Inject GetExamForEditAction and ListActiveCareersAction
    - Call actions to get exam and careers DTOs
    - Pass $exam and $careers variables to view
    - Update view path to 'admin.exams.edit'
    - _Requirements: 2.1, 3.1.1, 3.1.3, 4.1, 4.3, 5.1_
  
  - [x] 6.5 Update update method
    - Inject UpdateExamAction
    - Call action with validated data
    - Remove direct model access
    - _Requirements: 3.1.1, 3.1.4, 4.1, 4.4_
  
  - [x] 6.6 Update destroy method
    - Inject DeleteExamAction
    - Call action to delete exam
    - Handle exceptions for business rule violations
    - _Requirements: 3.1.1, 3.1.4, 4.1_

- [x] 7. Rename view folders from Portuguese to English
  - [x] 7.1 Rename carreiras folder to careers
    - Move laravel/resources/views/admin/carreiras to laravel/resources/views/admin/careers
    - _Requirements: 1.1, 1.2, 1.5_
  
  - [x] 7.2 Rename editais folder to notices
    - Move laravel/resources/views/admin/editais to laravel/resources/views/admin/notices
    - _Requirements: 1.1, 1.3, 1.5_
  
  - [x] 7.3 Rename simulados folder to exams
    - Move laravel/resources/views/admin/simulados to laravel/resources/views/admin/exams
    - _Requirements: 1.1, 1.4, 1.5_

- [x] 8. Update Career views with English variable names
  - [x] 8.1 Update careers/index.blade.php
    - Replace $carreiras with $careers in pagination and loop
    - Replace $carreira with $career in loop variable
    - Update property access to use DTO properties (name, slug, examsCount, active)
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 3.3_
  
  - [x] 8.2 Update careers/create.blade.php
    - Verify no variable changes needed (form only)
    - _Requirements: 2.1_
  
  - [x] 8.3 Update careers/edit.blade.php
    - Replace $carreira with $career throughout
    - Update property access to use DTO properties
    - _Requirements: 2.1, 2.2, 2.4, 2.5, 3.3_

- [x] 9. Update Notice views with English variable names
  - [x] 9.1 Update notices/index.blade.php
    - Replace $editais with $notices in pagination and loop
    - Replace $edital with $notice in loop variable
    - Update property access to use DTO properties
    - Update career relationship access to use nested DTO
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 3.3, 3.5_
  
  - [x] 9.2 Update notices/create.blade.php
    - Replace $carreiras with $careers in loop
    - Replace $carreira with $career in loop variable
    - Update property access to use DTO properties
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 3.3_
  
  - [x] 9.3 Update notices/edit.blade.php
    - Replace $edital with $notice throughout
    - Replace $carreiras with $careers in loop
    - Replace $carreira with $career in loop variable
    - Update property access to use DTO properties
    - _Requirements: 2.1, 2.2, 2.4, 2.5, 3.3, 3.5_

- [x] 10. Update Exam views with English variable names
  - [x] 10.1 Update exams/index.blade.php
    - Replace $simulados with $exams in pagination and loop
    - Replace $simulado with $exam in loop variable
    - Update property access to use DTO properties (title, timeLimitMinutes, totalQuestions, active)
    - Update career relationship access to use nested DTO
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 3.3, 3.5_
  
  - [x] 10.2 Update exams/create.blade.php
    - Replace $carreiras with $careers in loop
    - Replace $carreira with $career in loop variable
    - Update property access to use DTO properties
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 3.3_
  
  - [x] 10.3 Update exams/edit.blade.php
    - Replace $simulado with $exam throughout
    - Replace $carreiras with $careers in loop
    - Replace $carreira with $career in loop variable
    - Update property access to use DTO properties
    - _Requirements: 2.1, 2.2, 2.4, 2.5, 3.3, 3.5_

- [x] 11. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.
