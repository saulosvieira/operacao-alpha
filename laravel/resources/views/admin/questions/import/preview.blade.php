@extends('adminlte::page')

@section('title', 'Pré-visualização - Importação de Questões')

@section('content_header')
    <h1>Pré-visualização da Importação</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.import.questions.index') }}">Importação</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.import.sessions.mapping', $session->id) }}">Mapeamento de Carreiras</a></li>
        <li class="breadcrumb-item active">Pré-visualização</li>
    </ol>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Progress Steps -->
            <div class="card">
                <div class="card-body">
                    <div class="progress-steps">
                        <div class="step completed">
                            <i class="fas fa-upload"></i>
                            <span>Upload do Arquivo</span>
                        </div>
                        <div class="step completed">
                            <i class="fas fa-map-marked-alt"></i>
                            <span>Mapear Carreiras</span>
                        </div>
                        <div class="step active">
                            <i class="fas fa-eye"></i>
                            <span>Pré-visualização</span>
                        </div>
                        <div class="step">
                            <i class="fas fa-play"></i>
                            <span>Importar</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Import Statistics -->
            <div class="row">
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-info">
                            <i class="fas fa-file-alt"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total de Questões</span>
                            <span class="info-box-number">{{ number_format($preview['total_questions']) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-success">
                            <i class="fas fa-check"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Questões Válidas</span>
                            <span class="info-box-number">{{ number_format($preview['valid_questions']) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Questões Inválidas</span>
                            <span class="info-box-number">{{ number_format($preview['invalid_questions']) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary">
                            <i class="fas fa-percentage"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Taxa de Sucesso</span>
                            <span class="info-box-number">{{ number_format(($preview['valid_questions'] / $preview['total_questions']) * 100, 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Questions by Career -->
            @if (isset($preview['questions_by_career']) && count($preview['questions_by_career']) > 0)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie"></i>
                            Questões por Carreira
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Carreira</th>
                                        <th>Questões Válidas</th>
                                        <th>Questões Inválidas</th>
                                        <th>Total</th>
                                        <th>Taxa de Sucesso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($preview['questions_by_career'] as $careerData)
                                        <tr>
                                            <td><strong>{{ $careerData['career_name'] ?? 'Desconhecida' }}</strong></td>
                                            <td><span class="badge badge-success">{{ $careerData['valid_count'] ?? $careerData['valid_questions'] ?? 0 }}</span></td>
                                            <td><span class="badge badge-warning">{{ $careerData['invalid_count'] ?? ($careerData['question_count'] ?? 0) - ($careerData['valid_questions'] ?? 0) }}</span></td>
                                            <td>{{ $careerData['total_count'] ?? $careerData['question_count'] ?? 0 }}</td>
                                            <td>
                                                @php
                                                    $validCount = $careerData['valid_count'] ?? $careerData['valid_questions'] ?? 0;
                                                    $totalCount = $careerData['total_count'] ?? $careerData['question_count'] ?? 0;
                                                    $rate = $totalCount > 0 ? ($validCount / $totalCount) * 100 : 0;
                                                @endphp
                                                <span class="badge badge-{{ $rate >= 80 ? 'success' : ($rate >= 50 ? 'warning' : 'danger') }}">
                                                    {{ number_format($rate, 1) }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Validation Errors -->
            @php
                $errorsByRow = $preview['validation_errors']['errors_by_row'] ?? [];
                $totalErrorRows = $preview['validation_errors']['total_error_rows'] ?? count($errorsByRow);
            @endphp
            @if ($totalErrorRows > 0)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                            Erros de Validação ({{ $totalErrorRows }} linhas)
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle"></i>
                            As questões abaixo possuem erros de validação e serão ignoradas durante a importação. 
                            Você ainda pode prosseguir com a importação das questões válidas.
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="10%">Linha</th>
                                        <th width="20%">Carreira</th>
                                        <th width="50%">Mensagem de Erro</th>
                                        <th width="20%">Prévia da Questão</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (array_slice($errorsByRow, 0, 50, true) as $rowNumber => $error)
                                        <tr>
                                            <td><span class="badge badge-danger">{{ $error['row_number'] ?? $rowNumber }}</span></td>
                                            <td>{{ $error['data']['career_name'] ?? $error['data']['career_abbreviation'] ?? 'N/A' }}</td>
                                            <td>
                                                <small class="text-danger">
                                                    @foreach (array_slice($error['errors'] ?? [], 0, 3) as $errorMsg)
                                                        {{ $errorMsg }}<br>
                                                    @endforeach
                                                    @if (count($error['errors'] ?? []) > 3)
                                                        <em>... e mais {{ count($error['errors']) - 3 }}</em>
                                                    @endif
                                                </small>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ Str::limit($error['data']['statement'] ?? 'N/A', 50) }}
                                                </small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if ($totalErrorRows > 50)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Exibindo as primeiras 50 linhas com erro. Total de linhas com erros: {{ $totalErrorRows }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Sample Questions Preview -->
            @if (isset($preview['sample_questions']) && count($preview['sample_questions']) > 0)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-eye"></i>
                            Prévia das Questões (Primeiras 10)
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @foreach (array_slice($preview['sample_questions'] ?? [], 0, 10) as $index => $question)
                            <div class="card card-outline card-success mb-3">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        Questão {{ $index + 1 }} - {{ $question['career_name'] ?? 'Desconhecida' }}
                                        <span class="badge badge-info ml-2">Linha {{ $question['row_number'] ?? 'N/A' }}</span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="question-statement mb-3">
                                        <strong>Enunciado:</strong>
                                        <p>{{ $question['statement'] ?? 'N/A' }}</p>
                                    </div>
                                    
                                    <div class="question-options">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="option {{ ($question['correct_answer'] ?? '') === 'A' ? 'correct-answer' : '' }}">
                                                    <strong>A)</strong> {{ $question['option_a'] ?? 'N/A' }}
                                                </div>
                                                <div class="option {{ ($question['correct_answer'] ?? '') === 'B' ? 'correct-answer' : '' }}">
                                                    <strong>B)</strong> {{ $question['option_b'] ?? 'N/A' }}
                                                </div>
                                                <div class="option {{ ($question['correct_answer'] ?? '') === 'C' ? 'correct-answer' : '' }}">
                                                    <strong>C)</strong> {{ $question['option_c'] ?? 'N/A' }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="option {{ ($question['correct_answer'] ?? '') === 'D' ? 'correct-answer' : '' }}">
                                                    <strong>D)</strong> {{ $question['option_d'] ?? 'N/A' }}
                                                </div>
                                                @if (!empty($question['option_e']))
                                                <div class="option {{ ($question['correct_answer'] ?? '') === 'E' ? 'correct-answer' : '' }}">
                                                    <strong>E)</strong> {{ $question['option_e'] }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <strong>Resposta Correta:</strong> 
                                        <span class="badge badge-success">{{ $question['correct_answer'] ?? 'N/A' }}</span>
                                        
                                        @if (!empty($question['explanation']))
                                            <div class="mt-2">
                                                <strong>Explicação:</strong>
                                                <p class="text-muted">{{ $question['explanation'] }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Import Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs"></i>
                        Ações de Importação
                    </h3>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($preview['valid_questions'] > 0)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <strong>Pronto para Importar:</strong> {{ number_format($preview['valid_questions']) }} questões válidas serão importadas.
                            @if ($preview['invalid_questions'] > 0)
                                {{ number_format($preview['invalid_questions']) }} questões inválidas serão ignoradas.
                            @endif
                        </div>

                        <form action="{{ route('admin.import.sessions.execute', $session->id) }}" method="POST" id="import-form">
                            @csrf
                            
                            <!-- Exam Destination Mappings -->
                            <div class="card card-outline card-primary mb-3">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-bullseye"></i>
                                        Destino das Questões
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        Para cada carreira, escolha se deseja adicionar as questões a um simulado existente ou criar um novo.
                                    </div>
                                    
                                    @if (isset($preview['questions_by_career']))
                                        @foreach ($preview['questions_by_career'] as $index => $careerData)
                                            @if (($careerData['career_id'] ?? null) !== null)
                                            <div class="card mb-3">
                                                <div class="card-header bg-light">
                                                    <strong>{{ $careerData['career_name'] ?? 'Desconhecida' }}</strong>
                                                    <span class="badge badge-primary ml-2">{{ $careerData['valid_count'] ?? $careerData['valid_questions'] ?? 0 }} questões</span>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group mb-2">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" 
                                                                   id="exam_option_existing_{{ $careerData['career_id'] }}" 
                                                                   name="exam_option[{{ $careerData['career_id'] }}]" 
                                                                   value="existing" 
                                                                   class="custom-control-input exam-option-radio"
                                                                   data-career-id="{{ $careerData['career_id'] }}"
                                                                   checked>
                                                            <label class="custom-control-label" for="exam_option_existing_{{ $careerData['career_id'] }}">
                                                                Adicionar a simulado existente
                                                            </label>
                                                        </div>
                                                        <div class="ml-4 mt-2" id="existing_exam_container_{{ $careerData['career_id'] }}">
                                                            <select name="exam_mappings[{{ $careerData['career_id'] }}]" 
                                                                    class="form-control exam-select" 
                                                                    data-career-id="{{ $careerData['career_id'] }}">
                                                                <option value="">Carregando simulados...</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-group mb-0">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" 
                                                                   id="exam_option_new_{{ $careerData['career_id'] }}" 
                                                                   name="exam_option[{{ $careerData['career_id'] }}]" 
                                                                   value="new" 
                                                                   class="custom-control-input exam-option-radio"
                                                                   data-career-id="{{ $careerData['career_id'] }}">
                                                            <label class="custom-control-label" for="exam_option_new_{{ $careerData['career_id'] }}">
                                                                Criar novo simulado
                                                            </label>
                                                        </div>
                                                        <div class="ml-4 mt-2" id="new_exam_container_{{ $careerData['career_id'] }}" style="display: none;">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Nome do Simulado <span class="text-danger">*</span></label>
                                                                        <input type="text" 
                                                                               name="new_exam[{{ $careerData['career_id'] }}][title]" 
                                                                               class="form-control"
                                                                               placeholder="Ex: Simulado {{ $careerData['career_name'] }} - {{ date('m/Y') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label>Tempo Limite (min)</label>
                                                                        <input type="number" 
                                                                               name="new_exam[{{ $careerData['career_id'] }}][time_limit]" 
                                                                               class="form-control"
                                                                               value="180"
                                                                               min="30"
                                                                               max="480">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label>Modo de Feedback</label>
                                                                        <select name="new_exam[{{ $careerData['career_id'] }}][feedback_mode]" class="form-control">
                                                                            <option value="after_submit">Após enviar</option>
                                                                            <option value="immediate">Imediato</option>
                                                                            <option value="never">Nunca</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Descrição (opcional)</label>
                                                                <textarea name="new_exam[{{ $careerData['career_id'] }}][description]" 
                                                                          class="form-control" 
                                                                          rows="2"
                                                                          placeholder="Descrição do simulado..."></textarea>
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" 
                                                                       name="new_exam[{{ $careerData['career_id'] }}][is_free]" 
                                                                       class="custom-control-input"
                                                                       id="new_exam_free_{{ $careerData['career_id'] }}">
                                                                <label class="custom-control-label" for="new_exam_free_{{ $careerData['career_id'] }}">
                                                                    Simulado gratuito (disponível para todos)
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="{{ route('admin.import.sessions.mapping', $session->id) }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i>
                                            Voltar ao Mapeamento
                                        </a>
                                        <button type="button" class="btn btn-warning ml-2" onclick="cancelImport()">
                                            <i class="fas fa-times"></i>
                                            Cancelar Importação
                                        </button>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button type="submit" class="btn btn-success btn-lg" id="import-btn">
                                            <i class="fas fa-play"></i>
                                            Iniciar Importação ({{ number_format($preview['valid_questions']) }} questões)
                                        </button>
                                        
                                        <!-- Progress Modal -->
                                        <div class="modal fade" id="progress-modal" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            <i class="fas fa-cogs"></i>
                                                            Progresso da Importação
                                                        </h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="progress mb-3" style="height: 25px;">
                                                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                                                 role="progressbar" style="width: 0%" id="import-progress-bar">
                                                                <span id="progress-text">0%</span>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="info-box">
                                                                    <span class="info-box-icon bg-info"><i class="fas fa-list"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Processadas</span>
                                                                        <span class="info-box-number" id="processed-count">0</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="info-box">
                                                                    <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Sucesso</span>
                                                                        <span class="info-box-number" id="successful-count">0</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="info-box">
                                                                    <span class="info-box-icon bg-danger"><i class="fas fa-times"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Falhas</span>
                                                                        <span class="info-box-number" id="failed-count">0</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="info-box">
                                                                    <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                                                    <div class="info-box-content">
                                                                        <span class="info-box-text">Tempo Restante</span>
                                                                        <span class="info-box-number" id="time-remaining">--</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mt-3">
                                                            <small class="text-muted">
                                                                <strong>Uso de Memória:</strong> <span id="memory-usage">--</span> |
                                                                <strong>Lote:</strong> <span id="current-batch">--</span> de <span id="total-batches">--</span> |
                                                                <strong>Última Atualização:</strong> <span id="last-update">--</span>
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" id="close-progress-btn" disabled>
                                                            Fechar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Não é possível importar:</strong> Nenhuma questão válida encontrada. Verifique seu arquivo Excel e tente novamente.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('admin.import.sessions.mapping', $session->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i>
                                    Voltar ao Mapeamento
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-warning" onclick="cancelImport()">
                                    <i class="fas fa-times"></i>
                                    Cancelar Importação
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.progress-steps {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 20px 0;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    position: relative;
    color: #6c757d;
}

.step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 15px;
    left: 60%;
    width: 80%;
    height: 2px;
    background-color: #dee2e6;
    z-index: -1;
}

.step.completed {
    color: #28a745;
}

.step.completed:not(:last-child)::after {
    background-color: #28a745;
}

.step.active {
    color: #007bff;
    font-weight: bold;
}

.step i {
    font-size: 24px;
    margin-bottom: 5px;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: currentColor;
    color: white;
}

.step.completed i {
    background-color: #28a745;
}

.step.active i {
    background-color: #007bff;
}

.step span {
    font-size: 12px;
    text-align: center;
}

.option {
    padding: 5px 0;
}

.correct-answer {
    background-color: #d4edda;
    padding: 5px;
    border-radius: 3px;
    border-left: 3px solid #28a745;
}

.question-statement {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 4px solid #007bff;
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Load exam options on page load
    loadAllExamOptions();
    
    // Handle radio button changes for exam options
    $('.exam-option-radio').on('change', function() {
        const careerId = $(this).data('career-id');
        const option = $(this).val();
        
        if (option === 'existing') {
            $('#existing_exam_container_' + careerId).show();
            $('#new_exam_container_' + careerId).hide();
        } else {
            $('#existing_exam_container_' + careerId).hide();
            $('#new_exam_container_' + careerId).show();
        }
    });
    
    // Form submission with progress tracking
    $('#import-form').on('submit', function(e) {
        e.preventDefault();
        
        // Validate new exam fields if selected
        let valid = true;
        $('.exam-option-radio:checked').each(function() {
            const careerId = $(this).data('career-id');
            const option = $(this).val();
            
            if (option === 'new') {
                const titleInput = $(`input[name="new_exam[${careerId}][title]"]`);
                if (!titleInput.val().trim()) {
                    alert('Por favor, preencha o nome do simulado para todas as carreiras com "Criar novo simulado" selecionado.');
                    titleInput.focus();
                    valid = false;
                    return false;
                }
            }
        });
        
        // Validate existing exam selection
        $('.exam-option-radio:checked').each(function() {
            const careerId = $(this).data('career-id');
            const option = $(this).val();
            
            if (option === 'existing') {
                const examSelect = $(`select[name="exam_mappings[${careerId}]"]`);
                if (!examSelect.val()) {
                    alert('Por favor, selecione um simulado de destino para todas as carreiras com "Adicionar a simulado existente" selecionado.');
                    examSelect.focus();
                    valid = false;
                    return false;
                }
            }
        });
        
        if (!valid) return false;
        
        if (!confirm('Tem certeza que deseja iniciar a importação? Este processo não pode ser desfeito.')) {
            return false;
        }
        
        // Show progress modal
        $('#progress-modal').modal({
            backdrop: 'static',
            keyboard: false
        });
        
        // Start the import
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                // Start progress tracking
                startProgressTracking();
            },
            error: function(xhr) {
                $('#progress-modal').modal('hide');
                
                let errorMessage = 'Falha na importação. Por favor, tente novamente.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                alert(errorMessage);
            }
        });
    });
    
    let progressInterval;
    let sessionId = {{ $session->id }};
    
    function startProgressTracking() {
        progressInterval = setInterval(function() {
            $.get('{{ route("admin.import.sessions.progress", $session->id) }}', function(response) {
                if (response.success) {
                    updateProgressDisplay(response.data);
                    
                    // Check if import is completed
                    if (response.data.status === 'completed') {
                        clearInterval(progressInterval);
                        $('#close-progress-btn').prop('disabled', false);
                        
                        // Redirect to report after a short delay
                        setTimeout(function() {
                            window.location.href = '{{ route("admin.import.sessions.report", $session->id) }}';
                        }, 2000);
                    }
                }
            }).fail(function() {
                // If progress tracking fails, stop the interval and enable close button
                clearInterval(progressInterval);
                $('#close-progress-btn').prop('disabled', false);
            });
        }, 2000); // Update every 2 seconds
    }
    
    function updateProgressDisplay(data) {
        // Update progress bar
        $('#import-progress-bar').css('width', data.progress_percentage + '%');
        $('#progress-text').text(data.progress_percentage + '%');
        
        // Update counters
        $('#processed-count').text(data.processed_rows.toLocaleString());
        $('#successful-count').text(data.successful_rows.toLocaleString());
        $('#failed-count').text(data.failed_rows.toLocaleString());
        
        // Update time remaining
        if (data.estimated_time_remaining) {
            $('#time-remaining').text(formatTime(data.estimated_time_remaining));
        } else {
            $('#time-remaining').text('--');
        }
        
        // Update additional info
        $('#memory-usage').text(data.memory_usage);
        $('#current-batch').text(data.current_batch);
        $('#total-batches').text(data.total_batches);
        $('#last-update').text(data.last_update);
        
        // Update progress bar color based on status
        let progressBar = $('#import-progress-bar');
        progressBar.removeClass('bg-success bg-danger bg-warning');
        
        if (data.status === 'completed') {
            progressBar.addClass('bg-success');
            progressBar.removeClass('progress-bar-animated');
        } else if (data.failed_rows > data.successful_rows) {
            progressBar.addClass('bg-warning');
        }
    }
    
    function formatTime(seconds) {
        if (seconds < 60) {
            return Math.round(seconds) + 's';
        } else if (seconds < 3600) {
            return Math.round(seconds / 60) + 'm';
        } else {
            return Math.round(seconds / 3600) + 'h';
        }
    }
    
    // Handle close button
    $('#close-progress-btn').on('click', function() {
        if (progressInterval) {
            clearInterval(progressInterval);
        }
        $('#progress-modal').modal('hide');
    });
});

function loadAllExamOptions() {
    $('.exam-select').each(function() {
        const select = $(this);
        const careerId = select.data('career-id');
        
        select.html('<option value="">Carregando...</option>');
        
        $.get('{{ route("admin.import.career-exams") }}', { career_id: careerId }, function(response) {
            select.html('<option value="">-- Selecione um simulado --</option>');
            
            if (response.success && response.exams.length > 0) {
                response.exams.forEach(function(exam) {
                    select.append(`<option value="${exam.id}">${exam.title} (${exam.question_count} questões)</option>`);
                });
                // Auto-select the first (most recent) exam
                select.find('option:eq(1)').prop('selected', true);
            } else {
                select.html('<option value="" disabled>Nenhum simulado ativo - crie um novo abaixo</option>');
                // Auto-select "create new" option if no exams exist
                $(`#exam_option_new_${careerId}`).prop('checked', true).trigger('change');
            }
        }).fail(function() {
            select.html('<option value="" disabled>Falha ao carregar simulados</option>');
        });
    });
}

function cancelImport() {
    if (confirm('Tem certeza que deseja cancelar esta importação? Todo o progresso será perdido.')) {
        window.location.href = '{{ route("admin.import.sessions.cancel", $session->id) }}';
    }
}
</script>
@stop