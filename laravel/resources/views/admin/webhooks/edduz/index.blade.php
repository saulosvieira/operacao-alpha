@extends('adminlte::page')

@section('title', 'Histórico de Webhooks Edduz')

@section('content_header')
    <h1>Histórico de Webhooks Edduz</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('admin.webhooks.edduz.index') }}" class="form-inline flex-wrap" style="gap: 8px;">
                <div class="form-group mr-2">
                    <label for="status" class="mr-1">Status</label>
                    <select name="status" id="status" class="form-control form-control-sm">
                        <option value="">Todos</option>
                        <option value="success" {{ ($filters['status'] ?? '') === 'success' ? 'selected' : '' }}>Sucesso</option>
                        <option value="error" {{ ($filters['status'] ?? '') === 'error' ? 'selected' : '' }}>Erro</option>
                        <option value="duplicate" {{ ($filters['status'] ?? '') === 'duplicate' ? 'selected' : '' }}>Duplicado</option>
                        <option value="invalid_token" {{ ($filters['status'] ?? '') === 'invalid_token' ? 'selected' : '' }}>Token Inválido</option>
                    </select>
                </div>

                <div class="form-group mr-2">
                    <label for="event_type" class="mr-1">Tipo de Evento</label>
                    <select name="event_type" id="event_type" class="form-control form-control-sm">
                        <option value="">Todos</option>
                        <option value="subscription_confirmed" {{ ($filters['event_type'] ?? '') === 'subscription_confirmed' ? 'selected' : '' }}>Assinatura Confirmada</option>
                        <option value="subscription_cancelled" {{ ($filters['event_type'] ?? '') === 'subscription_cancelled' ? 'selected' : '' }}>Assinatura Cancelada</option>
                        <option value="subscription_expired" {{ ($filters['event_type'] ?? '') === 'subscription_expired' ? 'selected' : '' }}>Assinatura Expirada</option>
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
                <a href="{{ route('admin.webhooks.edduz.index') }}" class="btn btn-default btn-sm">
                    <i class="fas fa-times"></i> Limpar
                </a>
            </form>
        </div>

        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <thead>
                    <tr>
                        <th width="160">Data/Hora</th>
                        <th>Tipo de Evento</th>
                        <th width="100">User ID</th>
                        <th width="120">Status</th>
                        <th width="140">IP de Origem</th>
                        <th width="80">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->received_at->format('d/m/Y H:i:s') }}</td>
                            <td>
                                @switch($log->event_type)
                                    @case('subscription_confirmed')
                                        <span class="badge badge-success">Assinatura Confirmada</span>
                                        @break
                                    @case('subscription_cancelled')
                                        <span class="badge badge-warning">Assinatura Cancelada</span>
                                        @break
                                    @case('subscription_expired')
                                        <span class="badge badge-secondary">Assinatura Expirada</span>
                                        @break
                                    @default
                                        <span class="badge badge-light">{{ $log->event_type }}</span>
                                @endswitch
                            </td>
                            <td>{{ $log->user_id ?? '-' }}</td>
                            <td>
                                @switch($log->processing_status)
                                    @case('success')
                                        <span class="badge badge-success">Sucesso</span>
                                        @break
                                    @case('error')
                                        <span class="badge badge-danger">Erro</span>
                                        @break
                                    @case('duplicate')
                                        <span class="badge badge-info">Duplicado</span>
                                        @break
                                    @case('invalid_token')
                                        <span class="badge badge-dark">Token Inválido</span>
                                        @break
                                    @default
                                        <span class="badge badge-light">{{ $log->processing_status }}</span>
                                @endswitch
                            </td>
                            <td><code>{{ $log->ip_address }}</code></td>
                            <td>
                                <a href="{{ route('admin.webhooks.edduz.show', $log->id) }}"
                                   class="btn btn-sm btn-info" title="Ver detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Nenhum registro encontrado</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="card-footer">
                {{ $logs->appends($filters)->links() }}
            </div>
        @endif
    </div>
@stop
