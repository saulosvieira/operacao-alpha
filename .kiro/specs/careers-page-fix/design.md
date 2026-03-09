# Design Document

## Overview

Este documento descreve o design técnico para corrigir a funcionalidade da tela /carreiras. A solução envolve sincronizar as interfaces TypeScript do frontend com os dados fornecidos pelo backend, especificamente adicionando o campo `exams_count` que já é retornado pela API mas não está sendo utilizado na interface.

O backend já está correto e funcional - o `CareerRepository` utiliza `withCount` para incluir a contagem de simulados ativos. O problema está exclusivamente no frontend, onde:
1. As interfaces TypeScript não incluem o campo `exams_count`
2. O componente `Careers.tsx` não exibe a contagem de simulados
3. Há inconsistência entre diferentes definições da interface `Career`

## Architecture

### Current Architecture

```
Backend (Laravel)
├── CareerRepository.getAllActive()
│   └── Returns: Collection<CareerData>
│       └── Fields: id, name, description, active, exams_count
│
└── API Controller
    └── GET /api/careers
        └── Response: { data: Career[] }

Frontend (React + TypeScript)
├── services/careers.ts
│   ├── Interface Career (missing exams_count)
│   └── listCareers() → calls API
│
├── types/index.ts
│   └── Interface Career (missing exams_count)
│
└── pages/Careers.tsx
    └── Displays careers (not showing count)
```

### Target Architecture

```
Backend (Laravel) - NO CHANGES NEEDED
├── CareerRepository.getAllActive()
│   └── Returns: Collection<CareerData>
│       └── Fields: id, name, description, active, exams_count ✓
│
└── API Controller
    └── GET /api/careers
        └── Response: { data: Career[] } ✓

Frontend (React + TypeScript) - CHANGES HERE
├── services/careers.ts
│   ├── Interface Career (ADD exams_count) ✓
│   └── listCareers() → calls API ✓
│
├── types/index.ts
│   └── Interface Career (ADD exams_count) ✓
│
└── pages/Careers.tsx
    └── Displays careers (SHOW exams_count) ✓
```

## Components and Interfaces

### 1. TypeScript Interface Updates

#### types/index.ts

```typescript
export interface Career {
  id: string;
  name: string;
  description?: string;
  exams_count: number; // NEW FIELD
}
```

**Rationale**: Esta é a interface principal usada em todo o frontend. Adicionar `exams_count` aqui garante type safety em todos os componentes que consomem dados de carreiras.

#### services/careers.ts

```typescript
export interface Career {
  id: string;
  name: string;
  description?: string;
  active: boolean;
  exams_count: number; // NEW FIELD (replaces totalExams)
  totalExams?: number; // DEPRECATED - keep for backward compatibility
}
```

**Rationale**: Esta interface é específica do serviço de API. Mantemos `totalExams` como opcional e deprecated para não quebrar código existente durante a transição.

### 2. Component Updates

#### Careers.tsx - Display Logic

```typescript
// Current (not showing count):
<div className="flex items-center gap-1">
  <Users size={12} />
  <span>Simulados disponíveis</span>
</div>

// Updated (showing count):
<div className="flex items-center gap-1">
  <Users size={12} />
  <span>
    {career.exams_count} {career.exams_count === 1 ? 'simulado' : 'simulados'} 
    {career.exams_count === 0 ? ' (em breve)' : ' disponíveis'}
  </span>
</div>
```

**Rationale**: 
- Exibe a contagem real de simulados
- Usa singular/plural corretamente
- Adiciona feedback "(em breve)" para carreiras sem simulados
- Mantém o ícone `Users` para consistência visual

### 3. Service Layer

O serviço `careersService.listCareers()` já está correto e não precisa de alterações. Ele:
- Faz requisição GET para `/api/careers`
- Retorna `response.data.data` que já contém `exams_count`
- O TypeScript apenas não estava tipando corretamente

## Data Models

### Backend DTO (Already Correct)

```php
class CareerData
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public bool $active,
        public string $createdAt,
        public string $updatedAt,
        public string $slug = '',
        public int $examsCount = 0, // ✓ Already present
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'active' => $this->active,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'slug' => $this->slug,
            'exams_count' => $this->examsCount, // ✓ Correctly mapped
        ];
    }
}
```

### Frontend Interface (To Be Updated)

```typescript
interface Career {
  id: string;              // Maps to: CareerData.id
  name: string;            // Maps to: CareerData.name
  description?: string;    // Maps to: CareerData.description
  exams_count: number;     // Maps to: CareerData.examsCount → exams_count
}
```

### Data Flow

```
Database
  └── Career model (with exams relationship)
      └── CareerRepository.getAllActive()
          └── withCount(['exams' => active])
              └── CareerData DTO
                  └── toArray() → exams_count
                      └── API Response JSON
                          └── Frontend Career interface
                              └── React Component Display
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Interface Type Safety

*For any* Career object received from the API, the TypeScript compiler SHALL enforce that `exams_count` is present and is a number type.

**Validates: Requirements 2.1, 2.2**

### Property 2: Count Display Accuracy

*For any* Career with `exams_count = N`, the displayed text SHALL contain the exact number N without transformation.

**Validates: Requirements 1.1, 1.5**

### Property 3: Singular/Plural Grammar

*For any* Career, when `exams_count = 1` the display SHALL use singular form "simulado", and when `exams_count ≠ 1` the display SHALL use plural form "simulados".

**Validates: Requirements 1.3, 1.4**

### Property 4: Search Filter Consistency

*For any* search term S and career list C, the filtered results SHALL include career c if and only if S appears in c.name or c.description (case-insensitive).

**Validates: Requirements 3.1, 3.2, 3.3**

### Property 5: Active Careers Only

*For any* Career in the displayed list, that career SHALL have `active = true` in the backend.

**Validates: Requirements 6.1, 6.3**

### Property 6: Navigation ID Preservation

*For any* Career with id = X, clicking on that career SHALL navigate to URL `/carreiras/X/simulados` where X is preserved exactly.

**Validates: Requirements 4.1, 4.2**

### Property 7: Loading State Transitions

*For any* page load, the loading state SHALL transition from `true` → `false` exactly once, and SHALL be `false` when data is displayed or error is shown.

**Validates: Requirements 5.1, 5.2**

### Property 8: Error Message Display

*For any* API error response with message M, if M exists then the displayed error SHALL be M, otherwise the displayed error SHALL be the default "Erro ao carregar carreiras".

**Validates: Requirements 5.3, 5.4, 5.5**

### Property 9: Alphabetical Ordering

*For any* two adjacent careers C1 and C2 in the displayed list, C1.name SHALL be lexicographically less than or equal to C2.name (case-insensitive comparison).

**Validates: Requirements 6.4**

## Error Handling

### API Errors

```typescript
try {
  const data = await careersService.listCareers();
  setCareers(data);
} catch (err: any) {
  // Priority: API message > Generic message
  const errorMessage = err.response?.data?.message || 'Erro ao carregar carreiras';
  setError(errorMessage);
}
```

**Error Scenarios**:
1. **Network Error**: Display "Erro ao carregar carreiras"
2. **API Error (4xx/5xx)**: Display API message if available
3. **Timeout**: Display "Erro ao carregar carreiras"
4. **Invalid Response**: Display "Erro ao carregar carreiras"

### Empty States

```typescript
// No careers in database
if (careers.length === 0 && !searchTerm) {
  return <EmptyState message="Nenhuma carreira disponível" />;
}

// No search results
if (filteredCareers.length === 0 && searchTerm) {
  return <EmptyState message={`Nenhuma carreira encontrada para "${searchTerm}"`} />;
}
```

### Loading States

```typescript
// Initial load
if (isLoading) {
  return <LoadingSpinner message="Carregando carreiras..." />;
}

// Data loaded
return <CareersList careers={filteredCareers} />;
```

## Testing Strategy

### Unit Tests

Unit tests should focus on specific examples and edge cases:

1. **Interface Validation**
   - Test that Career interface includes exams_count
   - Test that exams_count is typed as number

2. **Display Logic**
   - Test singular form: `exams_count = 1` → "1 simulado disponível"
   - Test plural form: `exams_count = 5` → "5 simulados disponíveis"
   - Test zero case: `exams_count = 0` → "0 simulados (em breve)"

3. **Search Filtering**
   - Test exact match in name
   - Test partial match in description
   - Test case-insensitive matching
   - Test no results scenario

4. **Error Handling**
   - Test API error with message
   - Test API error without message
   - Test network error

### Property-Based Tests

Property tests should verify universal properties across all inputs (minimum 100 iterations per test):

1. **Property 2: Count Display Accuracy**
   ```typescript
   // Feature: careers-page-fix, Property 2: Count Display Accuracy
   test('displayed count matches exams_count for any career', () => {
     fc.assert(
       fc.property(
         fc.record({
           id: fc.string(),
           name: fc.string(),
           exams_count: fc.nat()
         }),
         (career) => {
           const display = renderCareerCount(career);
           expect(display).toContain(career.exams_count.toString());
         }
       ),
       { numRuns: 100 }
     );
   });
   ```

2. **Property 3: Singular/Plural Grammar**
   ```typescript
   // Feature: careers-page-fix, Property 3: Singular/Plural Grammar
   test('grammar is correct for any count', () => {
     fc.assert(
       fc.property(
         fc.nat(),
         (count) => {
           const text = formatSimuladosText(count);
           if (count === 1) {
             expect(text).toMatch(/\bsimulado\b/);
             expect(text).not.toMatch(/\bsimulados\b/);
           } else {
             expect(text).toMatch(/\bsimulados\b/);
           }
         }
       ),
       { numRuns: 100 }
     );
   });
   ```

3. **Property 4: Search Filter Consistency**
   ```typescript
   // Feature: careers-page-fix, Property 4: Search Filter Consistency
   test('search results are consistent with filter criteria', () => {
     fc.assert(
       fc.property(
         fc.array(fc.record({
           id: fc.string(),
           name: fc.string(),
           description: fc.option(fc.string()),
           exams_count: fc.nat()
         })),
         fc.string(),
         (careers, searchTerm) => {
           const filtered = filterCareers(careers, searchTerm);
           filtered.forEach(career => {
             const matchesName = career.name.toLowerCase().includes(searchTerm.toLowerCase());
             const matchesDesc = career.description?.toLowerCase().includes(searchTerm.toLowerCase());
             expect(matchesName || matchesDesc).toBe(true);
           });
         }
       ),
       { numRuns: 100 }
     );
   });
   ```

4. **Property 9: Alphabetical Ordering**
   ```typescript
   // Feature: careers-page-fix, Property 9: Alphabetical Ordering
   test('careers are displayed in alphabetical order', () => {
     fc.assert(
       fc.property(
         fc.array(fc.record({
           id: fc.string(),
           name: fc.string(),
           description: fc.option(fc.string()),
           exams_count: fc.nat()
         })),
         (careers) => {
           const displayed = sortCareers(careers);
           for (let i = 0; i < displayed.length - 1; i++) {
             const current = displayed[i].name.toLowerCase();
             const next = displayed[i + 1].name.toLowerCase();
             expect(current <= next).toBe(true);
           }
         }
       ),
       { numRuns: 100 }
     );
   });
   ```

### Integration Tests

1. **Full Page Render**
   - Test that page renders without errors
   - Test that API is called on mount
   - Test that careers are displayed after loading

2. **User Interactions**
   - Test search input updates filtered list
   - Test clicking career navigates to correct URL
   - Test error state displays correctly

### Testing Library Selection

- **Unit Tests**: Jest + React Testing Library
- **Property Tests**: fast-check (TypeScript property-based testing library)
- **Integration Tests**: Jest + React Testing Library + MSW (Mock Service Worker)

### Test Configuration

All property-based tests must:
- Run minimum 100 iterations (`numRuns: 100`)
- Include comment tag: `// Feature: careers-page-fix, Property N: [property text]`
- Reference the design document property number
- Reference the requirements clause it validates

## Implementation Notes

### No Backend Changes Required

O backend já está correto e funcional:
- ✓ `CareerRepository` usa `withCount` corretamente
- ✓ `CareerData` DTO inclui `examsCount`
- ✓ `toArray()` mapeia para `exams_count`
- ✓ API retorna dados corretos

### Frontend Changes Only

Todas as mudanças são no frontend:
1. Atualizar interface em `types/index.ts`
2. Atualizar interface em `services/careers.ts`
3. Atualizar componente `Careers.tsx` para exibir contagem
4. Adicionar testes

### Backward Compatibility

Durante a transição, manter:
- `totalExams` como campo opcional e deprecated
- Código existente que usa `totalExams` continuará funcionando
- Novo código deve usar `exams_count`

### Migration Path

1. Adicionar `exams_count` às interfaces (não quebra código existente)
2. Atualizar componente para usar `exams_count`
3. Adicionar testes
4. Remover `totalExams` em versão futura (após confirmar que não é usado)
