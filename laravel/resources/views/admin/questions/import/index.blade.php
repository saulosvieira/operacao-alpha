@extends('adminlte::page')

@section('title', 'Importação de Questões')

@section('content_header')
    <h1>Importação de Questões</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- File Upload Form -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-upload"></i>
                        Importar Questões do Excel
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Formatos suportados:</strong> Arquivos Excel (.xls, .xlsx) até 10MB
                    </div>

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

                    <form action="{{ route('admin.import.upload') }}" method="POST" enctype="multipart/form-data" id="upload-form">
                        @csrf
                        <div class="form-group">
                            <label for="excel_file">Selecionar Arquivo Excel</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('excel_file') is-invalid @enderror" 
                                           id="excel_file" name="excel_file" accept=".xls,.xlsx" required>
                                    <label class="custom-file-label" for="excel_file">Escolher arquivo...</label>
                                </div>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary" id="upload-btn">
                                        <i class="fas fa-upload"></i>
                                        Enviar e Processar
                                    </button>
                                </div>
                            </div>
                            @error('excel_file')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Tamanho máximo do arquivo: 10MB. Formatos suportados: .xls, .xlsx
                            </small>
                        </div>
                    </form>

                    <!-- Upload Progress -->
                    <div id="upload-progress" class="progress mt-3" style="display: none;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" style="width: 0%"></div>
                    </div>

                    <!-- Expected Excel Format -->
                    <div class="mt-4">
                        <h5>Formato Esperado do Excel</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th>carreira</th>
                                        <th>materia</th>
                                        <th>assunto</th>
                                        <th>enunciado</th>
                                        <th>alternativa_a</th>
                                        <th>alternativa_b</th>
                                        <th>alternativa_c</th>
                                        <th>alternativa_d</th>
                                        <th>alternativa_e</th>
                                        <th>correta</th>
                                        <th>comentario</th>
                                        <th>nivel_dificuldade</th>
                                        <th>texto_apoio</th>
                                        <th>link_pdf_apoio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="text-muted">
                                        <td>GM</td>
                                        <td>Português</td>
                                        <td>Interpretação de texto</td>
                                        <td>Assinale a alternativa em que o uso de acento gráfico está CORRETO...</td>
                                        <td>A questão está com uma frase correta quanto à concordância nominal...</td>
                                        <td>Aquele policial tem uma frase está no sentido denotativo...</td>
                                        <td>Não cabra a Lei. Siga-se as formas...</td>
                                        <td>Aqui é tempo e pena...</td>
                                        <td>Minhas a analogia de evidência...</td>
                                        <td>A</td>
                                        <td>A missão foi anulada de evidência...</td>
                                        <td>Médio</td>
                                        <td>Conforme o Art. 5º da CF/88, todos são iguais perante a lei...</td>
                                        <td>https://exemplo.com/material-apoio.pdf</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted">
                            <strong>Nota:</strong> A primeira linha deve conter os cabeçalhos das colunas conforme mostrado acima.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Recent Import Sessions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i>
                        Sessões de Importação Recentes
                    </h3>
                </div>
                <div class="card-body">
                    <div id="recent-imports">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin"></i>
                            Carregando sessões de importação recentes...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Load recent import statistics
    loadRecentImports();
    
    // Handle file input change
    $('#excel_file').on('change', function() {
        const fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
        
        // Validate file size
        const file = this.files[0];
        if (file && file.size > 10 * 1024 * 1024) { // 10MB
            alert('O tamanho do arquivo excede o limite de 10MB. Por favor, escolha um arquivo menor.');
            $(this).val('');
            $(this).next('.custom-file-label').html('Escolher arquivo...');
        }
    });
    
    // Handle form submission
    $('#upload-form').on('submit', function(e) {
        const file = $('#excel_file')[0].files[0];
        if (!file) {
            e.preventDefault();
            alert('Por favor, selecione um arquivo para enviar.');
            return;
        }
        
        // Show progress bar
        $('#upload-progress').show();
        $('#upload-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processando...');
        
        // Simulate progress (since we can't track actual upload progress easily)
        let progress = 0;
        const progressInterval = setInterval(function() {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;
            $('#upload-progress .progress-bar').css('width', progress + '%');
        }, 500);
        
        // Clear interval after 30 seconds (fallback)
        setTimeout(function() {
            clearInterval(progressInterval);
        }, 30000);
    });
});

function loadRecentImports() {
    $.get('{{ route("admin.import.statistics") }}', function(data) {
        let html = '';
        
        if (data.recent_imports && data.recent_imports.length > 0) {
            html += '<div class="table-responsive">';
            html += '<table class="table table-bordered table-striped">';
            html += '<thead><tr><th>ID</th><th>Arquivo</th><th>Data</th><th>Taxa de Sucesso</th><th>Questões</th><th>Ações</th></tr></thead>';
            html += '<tbody>';
            
            data.recent_imports.forEach(function(session) {
                html += '<tr>';
                html += '<td>' + session.id + '</td>';
                html += '<td><i class="fas fa-file-excel text-success"></i> ' + session.filename + '</td>';
                html += '<td>' + session.created_at + '</td>';
                html += '<td><span class="badge badge-' + (session.success_rate >= 80 ? 'success' : (session.success_rate >= 50 ? 'warning' : 'danger')) + '">' + session.success_rate + '%</span></td>';
                html += '<td>' + session.total_processed + '</td>';
                html += '<td><a href="{{ route("admin.import.sessions.report", ":id") }}" class="btn btn-sm btn-info"><i class="fas fa-chart-bar"></i> Ver Relatório</a></td>'.replace(':id', session.id);
                html += '</tr>';
            });
            
            html += '</tbody></table></div>';
            
            // Add summary statistics
            html += '<div class="row mt-3">';
            html += '<div class="col-md-3"><div class="info-box"><span class="info-box-icon bg-info"><i class="fas fa-upload"></i></span><div class="info-box-content"><span class="info-box-text">Total de Importações</span><span class="info-box-number">' + data.total_imports + '</span></div></div></div>';
            html += '<div class="col-md-3"><div class="info-box"><span class="info-box-icon bg-success"><i class="fas fa-check"></i></span><div class="info-box-content"><span class="info-box-text">Questões Importadas</span><span class="info-box-number">' + data.total_successful_questions + '</span></div></div></div>';
            html += '<div class="col-md-3"><div class="info-box"><span class="info-box-icon bg-danger"><i class="fas fa-times"></i></span><div class="info-box-content"><span class="info-box-text">Questões Falhadas</span><span class="info-box-number">' + data.total_failed_questions + '</span></div></div></div>';
            html += '<div class="col-md-3"><div class="info-box"><span class="info-box-icon bg-warning"><i class="fas fa-percentage"></i></span><div class="info-box-content"><span class="info-box-text">Taxa Média de Sucesso</span><span class="info-box-number">' + Math.round(data.average_success_rate) + '%</span></div></div></div>';
            html += '</div>';
        } else {
            html = '<div class="text-center text-muted"><i class="fas fa-inbox fa-3x mb-3"></i><p>Nenhuma sessão de importação encontrada.</p></div>';
        }
        
        $('#recent-imports').html(html);
    }).fail(function() {
        $('#recent-imports').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Falha ao carregar estatísticas de importação.</div>');
    });
}
</script>
@stop