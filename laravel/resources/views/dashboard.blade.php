@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1 class="mb-0">Bem-vindo à Operação Alpha</h1>
    <small class="text-muted">Plataforma de Simulados para Concursos Públicos</small>
@stop

@section('content')
<div class="row">
    <div class="col-lg-8 mb-3">
        <div class="card shadow">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <h4 class="mb-0">Operação Alpha - Sistema de Simulados</h4>
                </div>
                <p>Plataforma completa para gestão de simulados de concursos públicos. Gerencie carreiras, crie simulados, acompanhe o desempenho dos usuários e muito mais.</p>
                
                <div class="row mt-4">
                    <div class="col-md-4 mb-3">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3><i class="fas fa-graduation-cap"></i></h3>
                                <p>Carreiras</p>
                            </div>
                            <a href="{{ route('admin.careers.index') }}" class="small-box-footer">
                                Acessar <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><i class="fas fa-file-alt"></i></h3>
                                <p>Simulados</p>
                            </div>
                            <a href="#" class="small-box-footer">
                                Em breve <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><i class="fas fa-question-circle"></i></h3>
                                <p>Questões</p>
                            </div>
                            <a href="#" class="small-box-footer">
                                Em breve <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info mt-4 mb-0">
                    <i class="fas fa-info-circle"></i> <b>Sistema em desenvolvimento:</b> Novas funcionalidades serão adicionadas em breve!
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-3">
        <div class="card shadow h-100">
            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                <h5 class="mb-3">Atalhos Rápidos</h5>
                <a href="{{ route('admin.careers.index') }}" class="btn btn-outline-primary btn-block mb-2 w-100">
                    <i class="fas fa-graduation-cap"></i> Carreiras
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-info btn-block mb-2 w-100">
                    <i class="fas fa-users"></i> Usuários
                </a>
                <a href="{{ route('admin.import.questions.index') }}" class="btn btn-outline-warning btn-block mb-2 w-100">
                    <i class="fas fa-file-import"></i> Importar Questões
                </a>
                <a href="#" class="btn btn-outline-success btn-block w-100">
                    <i class="fas fa-chart-line"></i> Relatórios
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-graduation-cap"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Carreiras</span>
                <span class="info-box-number">{{ \App\Domain\Career\Models\Career::count() }}</span>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Usuários</span>
                <span class="info-box-number">{{ \App\Domain\Auth\Models\User::count() }}</span>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-warning"><i class="fas fa-file-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Simulados</span>
                <span class="info-box-number">{{ \App\Domain\Exam\Models\Exam::count() }}</span>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-danger"><i class="fas fa-question-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Questões</span>
                <span class="info-box-number">{{ \App\Domain\Exam\Models\Question::count() }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-purple"><i class="fas fa-file-import"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Importações</span>
                <span class="info-box-number">{{ \App\Domain\Import\Models\ImportSession::where('status', 'completed')->count() }}</span>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar"></i>
                    Estatísticas de Importação (Últimos 30 dias)
                </h3>
            </div>
            <div class="card-body" id="import-stats">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin"></i>
                    Carregando estatísticas...
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Load import statistics
    loadImportStatistics();
});

function loadImportStatistics() {
    $.get('{{ route("admin.import.statistics") }}', function(data) {
        let html = '<div class="row">';
        html += '<div class="col-md-3"><div class="info-box bg-info"><span class="info-box-icon"><i class="fas fa-upload"></i></span><div class="info-box-content"><span class="info-box-text">Total Imports</span><span class="info-box-number">' + data.total_imports + '</span></div></div></div>';
        html += '<div class="col-md-3"><div class="info-box bg-success"><span class="info-box-icon"><i class="fas fa-check"></i></span><div class="info-box-content"><span class="info-box-text">Questions Imported</span><span class="info-box-number">' + data.total_successful_questions + '</span></div></div></div>';
        html += '<div class="col-md-3"><div class="info-box bg-danger"><span class="info-box-icon"><i class="fas fa-times"></i></span><div class="info-box-content"><span class="info-box-text">Failed Questions</span><span class="info-box-number">' + data.total_failed_questions + '</span></div></div></div>';
        html += '<div class="col-md-3"><div class="info-box bg-warning"><span class="info-box-icon"><i class="fas fa-percentage"></i></span><div class="info-box-content"><span class="info-box-text">Avg Success Rate</span><span class="info-box-number">' + Math.round(data.average_success_rate) + '%</span></div></div></div>';
        html += '</div>';
        
        if (data.recent_imports && data.recent_imports.length > 0) {
            html += '<div class="mt-3"><h6>Recent Imports</h6><div class="table-responsive"><table class="table table-sm"><thead><tr><th>File</th><th>Date</th><th>Success Rate</th><th>Questions</th></tr></thead><tbody>';
            data.recent_imports.slice(0, 5).forEach(function(session) {
                html += '<tr><td>' + session.filename + '</td><td>' + session.created_at + '</td><td><span class="badge badge-' + (session.success_rate >= 80 ? 'success' : (session.success_rate >= 50 ? 'warning' : 'danger')) + '">' + session.success_rate + '%</span></td><td>' + session.total_processed + '</td></tr>';
            });
            html += '</tbody></table></div></div>';
        }
        
        $('#import-stats').html(html);
    }).fail(function() {
        $('#import-stats').html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Unable to load import statistics.</div>');
    });
}
</script>
