# Requirements Document

## Introduction

This document outlines the requirements for refactoring the Laravel application to remove Portuguese variable names and folder names from the views layer, ensuring consistency with the English naming convention already adopted in the domain layer and API.

## Glossary

- **View**: Laravel Blade template files that render HTML
- **Variable**: PHP variables passed to views and used within Blade templates
- **Folder Structure**: Directory organization within the resources/views path
- **Controller**: Laravel controller classes that pass data to views
- **Naming Convention**: The standardized approach to naming files, folders, and variables in English
- **DTO**: Data Transfer Object - a simple object that carries data between layers without business logic
- **Model**: Eloquent model representing database entities (should only be used in Repositories)
- **View Model**: Structured data representation using DTOs for view consumption
- **Action**: Domain layer class that executes business logic and returns DTOs
- **Repository**: Data access layer that interacts with Models and returns DTOs
- **Service**: Application layer class (note: not used in this architecture - Actions are preferred)

## Requirements

### Requirement 1

**User Story:** As a developer, I want all view folders to use English names, so that the codebase maintains consistent naming conventions throughout.

#### Acceptance Criteria

1. WHEN the application structure is examined THEN the system SHALL use English folder names for all view directories
2. WHEN views are organized by domain THEN the system SHALL use "careers" instead of "carreiras" for career-related views
3. WHEN views are organized by domain THEN the system SHALL use "notices" instead of "editais" for notice-related views
4. WHEN views are organized by domain THEN the system SHALL use "exams" instead of "simulados" for exam-related views
5. WHEN folder names are changed THEN the system SHALL update all references in routes, controllers, and view includes

### Requirement 2

**User Story:** As a developer, I want all variables in views to use English names, so that the code is readable and maintainable by international teams.

#### Acceptance Criteria

1. WHEN a view receives data from a controller THEN the system SHALL use English variable names
2. WHEN iterating over collections in views THEN the system SHALL use singular English names for loop variables (e.g., $career instead of $carreira)
3. WHEN displaying paginated data THEN the system SHALL use English variable names (e.g., $careers instead of $carreiras)
4. WHEN accessing object properties in views THEN the system SHALL use DTO properties with English names
5. WHEN variables are renamed THEN the system SHALL ensure all Blade directives (@foreach, @forelse, etc.) are updated accordingly

### Requirement 3

**User Story:** As a developer, I want views to only receive DTOs and never Eloquent models, so that the presentation layer is decoupled from the database layer.

#### Acceptance Criteria

1. WHEN a view receives data THEN the system SHALL only pass DTOs, never Eloquent models
2. WHEN views iterate over collections THEN the system SHALL receive collections of DTOs, not model collections
3. WHEN views access object properties THEN the system SHALL only access DTO properties, never model methods or attributes
4. WHEN paginated data is displayed THEN the system SHALL paginate DTO collections, not model queries
5. WHEN relationships are displayed THEN the system SHALL use nested DTOs instead of model relationships

### Requirement 3.1

**User Story:** As a developer, I want controllers to only interact with Actions and DTOs, so that controllers remain thin and focused on HTTP concerns.

#### Acceptance Criteria

1. WHEN a controller needs data THEN the system SHALL call an Action class from the domain layer
2. WHEN an Action returns data THEN the system SHALL return DTOs, never models
3. WHEN a controller receives DTOs THEN the system SHALL pass them directly to views without transformation
4. WHEN a controller needs to perform business logic THEN the system SHALL delegate to an Action, not implement logic in the controller
5. WHEN repositories are used THEN the system SHALL only be called from Actions, never from controllers

### Requirement 4

**User Story:** As a developer, I want controllers to pass English-named DTO variables to views, so that the data flow is consistent with the domain layer.

#### Acceptance Criteria

1. WHEN a controller method returns a view THEN the system SHALL pass DTO variables with English names
2. WHEN using compact() or array syntax THEN the system SHALL use English DTO variable names
3. WHEN passing multiple related entities THEN the system SHALL use consistent English naming for DTO collections (e.g., careers, notices, exams)
4. WHEN controllers are updated THEN the system SHALL convert models to DTOs before passing to views
5. WHEN variable names change THEN the system SHALL update all corresponding view references

### Requirement 5

**User Story:** As a developer, I want route names and view paths to remain unchanged, so that existing functionality continues to work without breaking changes.

#### Acceptance Criteria

1. WHEN folder structures are renamed THEN the system SHALL update view() calls to reflect new paths
2. WHEN view paths change THEN the system SHALL update all @include and @extends directives
3. WHEN refactoring is complete THEN the system SHALL maintain all existing route names
4. WHEN the application runs THEN the system SHALL render all views without errors
5. WHEN tests are executed THEN the system SHALL pass all existing test cases
