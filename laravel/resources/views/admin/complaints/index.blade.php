@extends('adminlte::page')

@section('title', 'Reclamações')

@section('content_header')
    <h1>Reclamações</h1>
@stop

@section('content')
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('admin.complaints.index') }}" class="form-inline flex-wrap" style="gap: 8px;">
                <div class="form-group mr-2">
                    <label for="status" class="mr-1">Status</label>
                    <select name="status" id="status" class="form-control form-control-sm">
                        <option value="">Todos</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s->value }}" {{ ($filters['status'] ?? '') == $s->value ? 'selected' : '' }}>
                                @switch($s->value)
                                    @case('open') Aberta @break
                                    @case('in_review') Em análise @break
                                    @case('resolved') Resolvida @break
                                    @case('rejected') Rejeitada @break
                                @endswitch
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mr-2">
                    <label for="type" class="mr-1">Tipo</label>
                    <select name="type" id="type" class="form-control form-control-sm">
                        <option value="">Todos</option>
                        @foreach($types as $t)
                            <option value="{{ $t->value }}" {{ ($filters['type'] ?? '') == $t->value ? 'selected' : '' }}>
                                @switch($t->value)
                                    @case('incorrect_answer') Gabarito incorreto @break
                                    @case('ambiguous_statement') Enunciado ambíguo @break
                                    @case('outdated_question') Questão desatualizada @break
                                    @case('formatting_error') Erro de formatação @break
                                    @case('other') Outro @break
                                @endswitch
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mr-2">
                    <label for="priority" class="mr-1">Prioridade</label>
                    <select name="priority" id="priority" class="form-control form-control-sm">
                        <option value="">Todas</option>
                        @foreach($priorities as $p)
                            <option value="{{ $p->value }}" {{ ($filters['priority'] ?? '') == $p->value ? 'selected' : '' }}>
                                @switch($p->value)
                                    @case('low') Baixa @break
                                    @case('medium') Média @break
                                    @case('high') Alta @break
                                @endswitch
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Filtrar
                </button>
                <a href="{{ route('admin.complaints.index') }}" class="btn btn-default btn-sm">
                    <i class="fas fa-times"></i> Limpar
                </a>
            </form>
        </div>

        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <thead>
                    <tr>
                        <th width="60">ID</th>
                        <th>Simulado</th>
                        <th width="100">Nº Questão</th>
                        <th>Tipo</th>
                        <th width="100">Prioridade</th>
                        <th width="120">Status</th>
                        <th>Administrador</th>
                        <th width="140">Data Criação</th>
                        <th width="80">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($complaints as $complaint)
                        <tr>
                            <td>{{ $complaint->id }}</td>
                            <td>{{ $complaint->exam_title }}</td>
                            <td>{{ $complaint->question_number }}</td>
                            <td>
                                @switch($complaint->type->value ?? $complaint->type)
                                    @case('incorrect_answer') Gabarito incorreto @break
                                    @case('ambiguous_statement') Enunciado ambíguo @break
                                    @case('outdated_question') Questão desatualizada @break
                                    @case('formatting_error') Erro de formatação @break
                                    @case('other') Outro @break
                                @endswitch
                            </td>
                            <td>
                                @switch($complaint->priority->value ?? $complaint->priority)
                                    @case('low') Baixa @break
                                    @case('medium') Média @break
                                    @case('high') Alta @break
                                @endswitch
                            </td>
                            <td>
                                @php
                                    $statusValue = $complaint->status->value ?? $complaint->status;
                                @endphp
                                @switch($statusValue)
                                    @case('open')
                                        <span class="badge badge-warning">Aberta</span>
                                        @break
                                    @case('in_review')
                                        <span class="badge badge-info">Em análise</span>
                                        @break
                                    @case('resolved')
                                        <span class="badge badge-success">Resolvida</span>
                                        @break
                                    @case('rejected')
                                        <span class="badge badge-secondary">Rejeitada</span>
                                        @break
                                @endswitch
                            </td>
                            <td>{{ $complaint->admin_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($complaint->created_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary btn-update-status"
                                        data-toggle="modal"
                                        data-target="#updateStatusModal"
                                        data-complaint-id="{{ $complaint->id }}"
                                        data-current-status="{{ $statusValue }}"
                                        title="Atualizar status">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Nenhuma reclamação encontrada</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            @if($complaints->hasPages())
                {{ $complaints->appends($filters)->links() }}
            @endif
        </div>
    </div>

    {{-- Modal para Atualizar Status --}}
    <div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="updateStatusForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateStatusModalLabel">Atualizar Status da Reclamação</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="modal_status">Novo Status</label>
                            <select name="status" id="modal_status" class="form-control" required>
                                <option value="open">Aberta</option>
                                <option value="in_review">Em análise</option>
                                <option value="resolved">Resolvida</option>
                                <option value="rejected">Rejeitada</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="modal_resolution_note">Nota de Resolução (opcional)</label>
                            <textarea name="resolution_note" id="modal_resolution_note" class="form-control" rows="3" placeholder="Descreva a resolução ou motivo da rejeição..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
$(document).ready(function() {
    $('.btn-update-status').on('click', function() {
        var complaintId = $(this).data('complaint-id');
        var currentStatus = $(this).data('current-status');
        var actionUrl = '{{ url("admin/complaints") }}/' + complaintId + '/status';

        $('#updateStatusForm').attr('action', actionUrl);
        $('#modal_status').val(currentStatus);
        $('#modal_resolution_note').val('');
    });
});
</script>
@stop