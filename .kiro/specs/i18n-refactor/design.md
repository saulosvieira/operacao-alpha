# Design Document: View Layer Internationalization Refactor

## Overview

This design document outlines the approach to refactor the Laravel admin views to use English naming conventions throughout, while enforcing proper architectural boundaries. The refactor will:

1. Rename view folders from Portuguese to English
2. Replace Portuguese variable names with English equivalents
3. Ensure controllers only interact with Actions and DTOs
4. Ensure views only receive DTOs, never Eloquent models
5. Create missing Actions for admin operations

The refactor maintains backward compatibility with routes and existing functionality while improving code maintainability and consistency.

## Architecture

### Current State Issues

The current implementation has several architectural violations:

1. **Controllers directly access Models**: Controllers like `CarreiraController` directly query `Career::withCount()` and `Career::create()`
2. **Views receive mixed data**: Some views receive DTOs (partially converted), others receive raw Models
3. **Inconsistent naming**: Portuguese variable names (`$carreiras`, `$editais`, `$simulados`) mixed with English folder structures
4. **Business logic in controllers**: Validation and data transformation happen in controllers instead of Actions

### Target Architecture

```
Request → Controller → Action → Repository → Model
                ↓         ↓
              View ← DTO ←┘
```

**Layer Responsibilities:**

- **Controller**: Handle HTTP concerns, call Actions, pass DTOs to views
- **Action**: Execute business logic, coordinate repositories, return DTOs
- **Repository**: Query models, transform to DTOs
- **View**: Display data from DTOs only
- **Model**: Database representation (only accessed by Repositories)

## Components and Interfaces

### 1. View Folder Structure

**Current:**
```
resources/views/admin/
├── carreiras/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── editais/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
└── simulados/
    ├── index.blade.php
    ├── create.blade.php
    └── edit.blade.php
```

**Target:**
```
resources/views/admin/
├── careers/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── notices/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
└── exams/
    ├── index.blade.php
    ├── create.blade.php
    └── edit.blade.php
```

### 2. New Actions Required

The following Actions need to be created to move business logic out of controllers:

#### Career Actions
- `App\Domain\Career\Actions\Admin\ListCareersForAdminAction`
- `App\Domain\Career\Actions\Admin\CreateCareerAction`
- `App\Domain\Career\Actions\Admin\UpdateCareerAction`
- `App\Domain\Career\Actions\Admin\DeleteCareerAction`
- `App\Domain\Career\Actions\Admin\GetCareerForEditAction`

#### Notice Actions
- `App\Domain\Career\Actions\Admin\ListNoticesForAdminAction`
- `App\Domain\Career\Actions\Admin\CreateNoticeAction`
- `App\Domain\Career\Actions\Admin\UpdateNoticeAction`
- `App\Domain\Career\Actions\Admin\DeleteNoticeAction`
- `App\Domain\Career\Actions\Admin\GetNoticeForEditAction`
- `App\Domain\Career\Actions\Admin\ListActiveCareersAction`

#### Exam Actions
- `App\Domain\Exam\Actions\Admin\ListExamsForAdminAction`
- `App\Domain\Exam\Actions\Admin\CreateExamAction`
- `App\Domain\Exam\Actions\Admin\UpdateExamAction`
- `App\Domain\Exam\Actions\Admin\DeleteExamAction`
- `App\Domain\Exam\Actions\Admin\GetExamForEditAction`

### 3. Action Interfaces

All Actions follow this pattern:

```php
class ListCareersForAdminAction
{
    public function __construct(
        private CareerRepository $repository
    ) {}
    
    public function execute(int $perPage = 15): LengthAwarePaginator
    {
        // Returns paginator of CareerData DTOs
    }
}
```

### 4. Controller Refactor Pattern

**Before:**
```php
public function index()
{
    $careers = Career::withCount('exams')->paginate(15);
    $carreiras = $careers->through(fn($c) => new CareerData(...));
    return view('admin.carreiras.index', compact('carreiras'));
}
```

**After:**
```php
public function index(ListCareersForAdminAction $action)
{
    $careers = $action->execute();
    return view('admin.careers.index', compact('careers'));
}
```

### 5. View Variable Mapping

| Current Variable | New Variable | Type |
|-----------------|--------------|------|
| `$carreiras` | `$careers` | `LengthAwarePaginator<CareerData>` |
| `$carreira` | `$career` | `CareerData` |
| `$editais` | `$notices` | `LengthAwarePaginator<NoticeData>` |
| `$edital` | `$notice` | `NoticeData` |
| `$simulados` | `$exams` | `LengthAwarePaginator<ExamData>` |
| `$simulado` | `$exam` | `ExamData` |

## Data Models

### Enhanced DTOs

Some DTOs need additional properties for admin views:

#### CareerData Enhancement
```php
readonly class CareerData
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public bool $active,
        public string $createdAt,
        public string $updatedAt,
        public string $slug,
        public int $examsCount,
    ) {}
}
```

#### NoticeData Enhancement
```php
readonly class NoticeData
{
    public function __construct(
        public int $id,
        public int $careerId,
        public string $title,
        public ?string $description,
        public ?string $examDate,
        public ?CareerData $career, // For display in lists
    ) {}
}
```

#### ExamData Enhancement
```php
readonly class ExamData
{
    public function __construct(
        public int $id,
        public int $careerId,
        public string $title,
        public ?string $description,
        public int $timeLimitMinutes,
        public bool $active,
        public int $totalQuestions,
        public ?CareerData $career, // For display in lists
    ) {}
}
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: View folder paths are English
*For any* view file in the admin section, the folder path should use English names (careers, notices, exams) and not Portuguese names (carreiras, editais, simulados)
**Validates: Requirements 1.1, 1.2, 1.3, 1.4**

### Property 2: Views receive only DTOs
*For any* variable passed to a view, it should be either a DTO instance, a collection of DTOs, or a paginator of DTOs, never an Eloquent model
**Validates: Requirements 3.1, 3.2, 3.3**

### Property 3: Controllers delegate to Actions
*For any* controller method that performs business logic, it should call an Action class and receive DTOs back, not query models directly
**Validates: Requirements 3.1.1, 3.1.2, 3.1.4**

### Property 4: Variable names are English
*For any* variable in a Blade template, the variable name should be in English (e.g., $career, $careers) not Portuguese (e.g., $carreira, $carreiras)
**Validates: Requirements 2.1, 2.2, 2.3**

### Property 5: View references are updated
*For any* view() call, @include, or @extends directive, the path should reference the new English folder names
**Validates: Requirements 5.1, 5.2**

## Error Handling

### Validation Errors
- Form validation remains in Request classes
- Actions throw domain exceptions for business rule violations
- Controllers catch exceptions and return appropriate responses

### Not Found Errors
- Route model binding continues to work with English parameter names
- 404 responses for missing resources

### Business Logic Errors
- Actions return Result objects or throw typed exceptions
- Examples: `CannotDeleteCareerWithExamsException`

## Testing Strategy

### Unit Tests
- Test each Action independently with mocked repositories
- Test DTO construction with various data combinations
- Test view rendering with sample DTO data

### Property-Based Tests
- Use Pest PHP with property testing
- Minimum 100 iterations per property test
- Each property test tagged with: `Feature: i18n-refactor, Property {number}: {property_text}`

### Integration Tests
- Test full request → controller → action → repository flow
- Verify views render correctly with DTO data
- Test form submissions and redirects

### Test Coverage Requirements
- All new Actions must have unit tests
- All refactored controllers must have integration tests
- All refactored views must have rendering tests
