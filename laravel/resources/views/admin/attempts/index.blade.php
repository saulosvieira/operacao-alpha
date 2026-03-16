@extends('adminlte::page')

@section('title', 'Tentativas de Simulados')

@section('content_header')
    <h1>Tentativas de Simulados</h1>
@stop

@section('content')
    {{-- Painel de Estatísticas --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($statistics->totalAttempts) }}</h3>
                    <p>Total de Tentativas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($statistics->avgScore, 1) }}</h3>
                    <p>Nota Média</p>
                </div>
                <div class="icon">
                    <i class="fas fa-star"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($statistics->avgAccuracy, 1) }}%</h3>
                    <p>Taxa Média de Acerto</p>
                </div>
                <div class="icon">
                    <i class="fas fa-bullseye"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ number_format($statistics->avgDurationMinutes, 0) }} min</h3>
                    <p>Tempo Médio</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Card com Filtros e Tabela --}}
    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('admin.attempts.index') }}" class="form-inline flex-wrap" style="gap: 8px;">
                <div class="form-group mr-2">
                    <label for="search" class="mr-1">Busca</label>
                    <input type="text" name="search" id="search" class="form-control form-control-sm"
                           placeholder="Nome ou título do simulado"
                           value="{{ $filters['search'] ?? '' }}">
                </div>

                <div class="form-group mr-2">
                    <label for="exam_id" class="mr-1">Simulado</label>
                    <select name="exam_id" id="exam_id" class="form-control form-control-sm">
                        <option value="">Todos</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}" {{ ($filters['exam_id'] ?? '') == $exam->id ? 'selected' : '' }}>
                                {{ $exam->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mr-2">
                    <label for="career_id" class="mr-1">Carreira</label>
                    <select name="career_id" id="career_id" class="form-control form-control-sm">
                        <option value="">Todas</option>
                        @foreach($careers as $career)
                            <option value="{{ $career->id }}" {{ ($filters['career_id'] ?? '') == $career->id ? 'selected' : '' }}>
                                {{ $career->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mr-2">
                    <label for="date_from" class="mr-1">De</label>
                    <input type="date" name="date_from" id="date_from" class="form-control form-control-sm"
                           value="{{ $filters['date_from'] ?? '' }}">
                </div>

                <div class="form-group mr-2">
                    <label for="date_to" class="mr-1">Até</label>
                    <input type="date" name="date_to" id="date_to" class="form-control form-control-sm"
                           value="{{ $filters['date_to'] ?? '' }}">
                </div>

                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Filtrar
                </button>
                <a href="{{ route('admin.attempts.index') }}" class="btn btn-default btn-sm">
                    <i class="fas fa-times"></i> Limpar
                </a>
            </form>
        </div>

        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <thead>
                    <tr>
                        <th width="60">ID</th>
                        <th>Usuário</th>
                        <th>Simulado</th>
                        <th>Carreira</th>
                        <th width="80">Nota</th>
                        <th width="110">Acertos/Total</th>
                        <th width="90">Duração</th>
                        <th width="140">Finalização</th>
                        <th width="70">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attempts as $attempt)
                        <tr>
                            <td>{{ $attempt->id }}</td>
                            <td>{{ $attempt->userName }}</td>
                            <td>
                                {{ $attempt->examTitle }}
                                @if($attempt->pendingComplaints > 0)
                                    <span class="badge badge-warning" title="{{ $attempt->pendingComplaints }} reclamação(ões) pendente(s)">
                                        <i class="fas fa-exclamation-triangle"></i> {{ $attempt->pendingComplaints }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $attempt->careerName }}</td>
                            <td>{{ number_format($attempt->score, 1) }}</td>
                            <td>{{ $attempt->correctAnswers }}/{{ $attempt->totalQuestions }}</td>
                            <td>{{ $attempt->durationMinutes }} min</td>
                            <td>{{ \Carbon\Carbon::parse($attempt->finishedAt)->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.attempts.show', $attempt->id) }}"
                                   class="btn btn-sm btn-info" title="Ver detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Nenhuma tentativa encontrada</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <div>
                @if($attempts->hasPages())
                    {{ $attempts->appends($filters)->links() }}
                @endif
            </div>
            <button type="button" id="btn-export-csv" class="btn btn-success btn-sm">
                <i class="fas fa-file-csv"></i> Exportar CSV
            </button>
        </div>
    </div>
@stop

@section('js')
<script>
$(document).ready(function() {
    $('#btn-export-csv').on('click', function() {
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Verificando...');

        var params = new URLSearchParams(window.location.search);
        var countUrl = '{{ route("admin.attempts.export.count") }}?' + params.toString();

        $.get(countUrl, function(data) {
            btn.prop('disabled', false).html('<i class="fas fa-file-csv"></i> Exportar CSV');

            if (data.count === 0) {
                alert('Nenhuma tentativa encontrada para exportar.');
                return;
            }

            if (data.count > 10000) {
                if (!confirm('A exportação contém ' + data.count.toLocaleString('pt-BR') + ' registros. Isso pode demorar. Deseja continuar?')) {
                    return;
                }
            }

            window.location.href = '{{ route("admin.attempts.export") }}?' + params.toString();
        }).fail(function() {
            btn.prop('disabled', false).html('<i class="fas fa-file-csv"></i> Exportar CSV');
            alert('Erro ao verificar a quantidade de registros. Tente novamente.');
        });
    });
});
</script>
@stop