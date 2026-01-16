@extends('adminlte::page')

@section('title', 'Relatório de Importação')

@section('content_header')
    <h1>Relatório de Importação de Questões</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.import.questions.index') }}">Importação</a></li>
        <li class="breadcrumb-item active">Relatório</li>
    </ol>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Session Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i>
                        Informações da Sessão de Importação
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID da Sessão:</strong> {{ $report['session']['id'] }}</p>
                            <p><strong>Arquivo:</strong> {{ $report['session']['filename'] }}</p>
                            <p><strong>Total de Linhas:</strong> {{ $report['session']['total_rows'] }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge badge-{{ $session->isCompleted() ? 'success' : ($session->hasFailed() ? 'danger' : 'warning') }}">
                                    @switch($report['session']['status'])
                                        @case('completed')
                                            Concluído
                                            @break
                                        @case('failed')
                                            Falhou
                                            @break
                                        @case('processing')
                                            Processando
                                            @break
                                        @case('uploaded')
                                            Enviado
                                            @break
                                        @case('mapped')
                                            Mapeado
                                            @break
                                        @case('previewed')
                                            Pré-visualizado
                                            @break
                                        @default
                                            {{ ucfirst($report['session']['status']) }}
                                    @endswitch
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Data de Criação:</strong> {{ $report['session']['created_at'] }}</p>
                            <p><strong>Criado por:</strong> {{ $report['session']['created_by'] }}</p>
                            <p><strong>Mapeamentos de Carreira:</strong> {{ $report['session']['career_mappings_count'] }}</p>
                            <p><strong>Tempo de Processamento:</strong> {{ $report['statistics']['processing_time'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i>
                        Estatísticas da Importação
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-list"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Processado</span>
                                    <span class="info-box-number">{{ number_format($report['statistics']['total_processed']) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Importadas com Sucesso</span>
                                    <span class="info-box-number">{{ number_format($report['statistics']['successful_imports']) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger"><i class="fas fa-times"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Falhas</span>
                                    <span class="info-box-number">{{ number_format($report['statistics']['failed_imports']) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-percentage"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Taxa de Sucesso</span>
                                    <span class="info-box-number">{{ $report['statistics']['success_rate'] }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Affected Exams -->
            @if(!empty($report['affected_exams']))
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt"></i>
                        Simulados Afetados
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nome do Simulado</th>
                                    <th>Carreira</th>
                                    <th>Questões Adicionadas</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report['affected_exams'] as $exam)
                                <tr>
                                    <td>{{ $exam['exam_name'] }}</td>
                                    <td>{{ $exam['career_name'] }}</td>
                                    <td>
                                        <span class="badge badge-success">{{ $exam['questions_added'] }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ $exam['view_url'] }}" class="btn btn-sm btn-info" target="_blank">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        <a href="{{ $exam['edit_url'] }}" class="btn btn-sm btn-primary" target="_blank">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Success Details -->
            @if(!empty($report['success_details']))
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-check-circle text-success"></i>
                        Detalhes das Questões Importadas por Simulado
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($report['success_details'] as $examId => $examData)
                    <div class="card card-outline card-success mb-3">
                        <div class="card-header">
                            <h4 class="card-title">{{ $examData['exam_name'] ?? 'Simulado Desconhecido' }}</h4>
                            <div class="card-tools">
                                <span class="badge badge-success">{{ $examData['questions_imported'] }} questões</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p><strong>Carreira:</strong> {{ $examData['career_name'] ?? 'Carreira Desconhecida' }}</p>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="15%">Nº da Questão</th>
                                            <th width="65%">Prévia do Enunciado</th>
                                            <th width="20%">Linha de Origem</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($examData['question_details'] ?? [] as $question)
                                        <tr>
                                            <td>{{ $question['question_number'] ?? '-' }}</td>
                                            <td>{{ $question['statement_preview'] ?? '-' }}</td>
                                            <td>{{ $question['row_number'] ?? '-' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Nenhum detalhe disponível</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Error Details -->
            @if($report['error_details']['total_errors'] > 0)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle text-danger"></i>
                        Detalhes dos Erros
                    </h3>
                    <div class="card-tools">
                        @if(!empty($report['downloadable_reports']['error_log']))
                        <a href="{{ route('admin.import.download-error-log', $session->id) }}" 
                           class="btn btn-sm btn-danger">
                            <i class="fas fa-download"></i> Baixar Log de Erros
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <p><strong>Total de Erros:</strong> {{ $report['error_details']['total_errors'] }}</p>
                    
                    <!-- Errors by Type -->
                    @if(!empty($report['error_details']['errors_by_type']))
                    <h5>Erros por Tipo</h5>
                    <div class="row mb-3">
                        @foreach($report['error_details']['errors_by_type'] as $type => $errors)
                        <div class="col-md-4">
                            <div class="alert alert-danger">
                                <strong>
                                    @switch($type)
                                        @case('validation')
                                            Validação
                                            @break
                                        @case('duplicate')
                                            Duplicata
                                            @break
                                        @case('database')
                                            Banco de Dados
                                            @break
                                        @case('exam_mapping')
                                            Mapeamento de Simulado
                                            @break
                                        @default
                                            {{ $type }}
                                    @endswitch
                                :</strong> {{ count($errors) }} erro(s)
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Detailed Errors -->
                    <h5>Lista Detalhada de Erros</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th width="10%">Linha</th>
                                    <th width="15%">Tipo de Erro</th>
                                    <th width="15%">Campo</th>
                                    <th width="45%">Mensagem</th>
                                    <th width="15%">Data/Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($report['error_details']['detailed_errors'] as $error)
                                <tr>
                                    <td><span class="badge badge-danger">{{ $error['row_number'] ?? '-' }}</span></td>
                                    <td>
                                        <span class="badge badge-warning">
                                            @switch($error['error_type'] ?? '')
                                                @case('validation')
                                                    Validação
                                                    @break
                                                @case('duplicate')
                                                    Duplicata
                                                    @break
                                                @case('database')
                                                    Banco de Dados
                                                    @break
                                                @case('exam_mapping')
                                                    Mapeamento
                                                    @break
                                                @default
                                                    {{ $error['error_type'] ?? 'Desconhecido' }}
                                            @endswitch
                                        </span>
                                    </td>
                                    <td>{{ $error['field'] ?? '-' }}</td>
                                    <td>{{ $error['message'] ?? 'Erro desconhecido' }}</td>
                                    <td><small>{{ $error['timestamp'] ?? '-' }}</small></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Nenhum erro detalhado disponível</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs"></i>
                        Opções de Exportação
                    </h3>
                </div>
                <div class="card-body">
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.import.export-report', $session->id) }}" 
                           class="btn btn-info">
                            <i class="fas fa-download"></i> Exportar Relatório Completo (JSON)
                        </a>
                        @if(!empty($report['downloadable_reports']['error_log']))
                        <a href="{{ route('admin.import.download-error-log', $session->id) }}" 
                           class="btn btn-danger">
                            <i class="fas fa-download"></i> Baixar Log de Erros (TXT)
                        </a>
                        @endif
                    </div>
                    
                    <div class="float-right">
                        <a href="{{ route('admin.import.questions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar para Importação
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .info-box-number {
        font-size: 1.5rem;
        font-weight: bold;
    }
    .card-outline {
        border-top: 3px solid;
    }
    .table-sm th,
    .table-sm td {
        padding: 0.3rem 0.5rem;
    }
</style>
@stop
