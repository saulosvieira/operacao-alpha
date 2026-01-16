# Requirements Document

## Introduction

Este documento especifica os requisitos para a implementação da funcionalidade de importação de questões via planilha XLS/XLSX no sistema Operação Alfa. A funcionalidade permitirá que administradores importem questões em lote para simulados, mapeando automaticamente as siglas das carreiras para os simulados correspondentes no sistema.

O sistema já possui a estrutura básica de simulados e questões implementada, mas falta a funcionalidade de importação em massa que facilite a criação de simulados com muitas questões de forma eficiente.

## Glossary

- **Excel_File**: Arquivo de planilha nos formatos .xls ou .xlsx contendo questões para importação
- **Question_Row**: Linha da planilha representando uma questão com todas suas informações
- **Career_Mapping**: Mapeamento entre siglas da planilha e carreiras cadastradas no sistema
- **Import_Session**: Sessão de importação que processa um arquivo Excel específico
- **Validation_Error**: Erro encontrado durante a validação dos dados da planilha
- **Import_Preview**: Visualização prévia dos dados que serão importados antes da confirmação
- **Batch_Import**: Processo de importação em lote de múltiplas questões
- **Column_Mapping**: Mapeamento entre colunas da planilha e campos do sistema
- **Question_Template**: Estrutura padrão esperada para as questões na planilha
- **Admin_Panel**: Painel administrativo Laravel/Blade para gerenciamento do sistema

## Requirements

### Requirement 1

**User Story:** As an administrator, I want to upload an Excel file with questions, so that I can import multiple questions at once instead of creating them individually.

#### Acceptance Criteria

1. WHEN an administrator accesses the question import page THEN the Admin_Panel SHALL display a file upload form accepting .xls and .xlsx files up to 10MB
2. WHEN an administrator selects an Excel file THEN the Admin_Panel SHALL validate the file format and size before allowing upload
3. WHEN an administrator uploads a valid Excel file THEN the System SHALL read the file and extract all question data from the first worksheet
4. WHEN the Excel file contains invalid format or is corrupted THEN the Admin_Panel SHALL display a clear error message and prevent processing
5. WHEN the Excel file exceeds the size limit THEN the Admin_Panel SHALL display a file size error and prevent upload

### Requirement 2

**User Story:** As an administrator, I want to map career abbreviations from the Excel file to existing careers in the system, so that questions are correctly associated with the right exams.

#### Acceptance Criteria

1. WHEN the System processes an Excel file THEN the System SHALL extract all unique career abbreviations from the designated column
2. WHEN career abbreviations are found THEN the Admin_Panel SHALL display a mapping interface showing each abbreviation with a dropdown to select the corresponding career from the database
3. WHEN an administrator maps abbreviations to careers THEN the System SHALL validate that all selected careers exist and are active
4. WHEN an abbreviation cannot be mapped to any career THEN the Admin_Panel SHALL highlight it as unmapped and require administrator action
5. WHEN all abbreviations are successfully mapped THEN the Admin_Panel SHALL enable the preview functionality

### Requirement 3

**User Story:** As an administrator, I want to preview the questions before importing, so that I can verify the data is correct and make adjustments if needed.

#### Acceptance Criteria

1. WHEN career mappings are complete THEN the Admin_Panel SHALL display a preview showing the first 10 questions with all their data (statement, options, correct answer, career)
2. WHEN displaying the preview THEN the Admin_Panel SHALL show the total number of questions to be imported and group them by career
3. WHEN the administrator reviews the preview THEN the Admin_Panel SHALL display any validation warnings or errors found in the question data
4. WHEN questions have missing required fields THEN the Admin_Panel SHALL highlight these issues in the preview with clear error messages
5. WHEN the administrator confirms the preview THEN the Admin_Panel SHALL proceed to the actual import process

### Requirement 4

**User Story:** As an administrator, I want the system to validate question data during import, so that only complete and valid questions are added to the database.

#### Acceptance Criteria

1. WHEN processing each question row THEN the System SHALL validate that the statement field is not empty and contains at least 10 characters
2. WHEN processing question options THEN the System SHALL validate that all five options (A, B, C, D, E) contain text and none are empty
3. WHEN processing the correct answer THEN the System SHALL validate that it is one of the valid options (A, B, C, D, or E)
4. WHEN processing career information THEN the System SHALL validate that the mapped career exists and has at least one active exam
5. WHEN validation fails for any question THEN the System SHALL log the error with row number and field details but continue processing other questions

### Requirement 5

**User Story:** As an administrator, I want questions to be automatically assigned to the appropriate exam based on career mapping, so that they are immediately available for use.

#### Acceptance Criteria

1. WHEN a valid question is processed THEN the System SHALL identify the target exam based on the career mapping and exam selection rules
2. WHEN multiple exams exist for a career THEN the Admin_Panel SHALL require the administrator to specify which exam should receive the imported questions
3. WHEN assigning question numbers THEN the System SHALL automatically assign sequential numbers starting from the next available number in the target exam
4. WHEN a question is successfully imported THEN the System SHALL create the question record with all validated data and associate it with the correct exam
5. WHEN all questions are processed THEN the System SHALL update the question count for each affected exam

### Requirement 6

**User Story:** As an administrator, I want to see a detailed import report after the process completes, so that I know exactly what was imported and what failed.

#### Acceptance Criteria

1. WHEN the import process completes THEN the Admin_Panel SHALL display a comprehensive report showing total questions processed, successful imports, and failed imports
2. WHEN questions fail to import THEN the Admin_Panel SHALL list each failed question with its row number, error description, and the problematic data
3. WHEN questions are successfully imported THEN the Admin_Panel SHALL group them by exam and show the count of questions added to each exam
4. WHEN displaying the report THEN the Admin_Panel SHALL provide options to download the error log as a text file for further analysis
5. WHEN the import is complete THEN the Admin_Panel SHALL provide a link to view the affected exams with their newly imported questions

### Requirement 7

**User Story:** As a system, I want to handle Excel file parsing reliably, so that different Excel formats and structures are properly processed.

#### Acceptance Criteria

1. WHEN reading Excel files THEN the System SHALL support both .xls (Excel 97-2003) and .xlsx (Excel 2007+) formats
2. WHEN parsing the Excel file THEN the System SHALL automatically detect the header row and map columns based on expected column names or positions
3. WHEN the Excel file has merged cells or complex formatting THEN the System SHALL extract only the text content and ignore formatting
4. WHEN encountering empty rows THEN the System SHALL skip them and continue processing the next row with data
5. WHEN the Excel file contains special characters or accents THEN the System SHALL preserve the original text encoding correctly

### Requirement 8

**User Story:** As an administrator, I want to handle import errors gracefully, so that partial failures don't prevent successful questions from being imported.

#### Acceptance Criteria

1. WHEN the import process encounters errors THEN the System SHALL continue processing remaining questions rather than stopping completely
2. WHEN database errors occur during import THEN the System SHALL log the error details and mark the affected questions as failed
3. WHEN memory or processing limits are reached THEN the System SHALL process questions in batches to avoid timeouts
4. WHEN duplicate questions are detected THEN the System SHALL skip them and log a warning rather than creating duplicates
5. WHEN the import process is interrupted THEN the System SHALL maintain data integrity and allow the administrator to retry with the same file
