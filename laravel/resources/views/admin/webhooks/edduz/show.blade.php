@extends('adminlte::page')

@section('title', 'Detalhe do Webhook Edduz #' . $log->id)

@section('content_header')
    <h1>Detalhe do Webhook Edduz <small class="text-muted">#{{ $log->id }}</small></h1>
@stop

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.webhooks.edduz.index') }}" class="btn btn-default">
            <i class="fas fa-arrow-left"></i> Voltar à listagem
        </a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informações Gerais</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <th width="160">ID</th>
                            <td>{{ $log->id }}</td>
                        </tr>
                        <tr>
                            <th>Transaction ID</th>
                            <td>{{ $log->transaction_id ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tipo de Evento</th>
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
                        </tr>
                        <tr>
                            <th>User ID</th>
                            <td>{{ $log->user_id ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
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
                        </tr>
                        <tr>
                            <th>IP de Origem</th>
                            <td><code>{{ $log->ip_address }}</code></td>
                        </tr>
                        <tr>
                            <th>Recebido em</th>
                            <td>{{ $log->received_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Processado em</th>
                            <td>{{ $log->processed_at ? $log->processed_at->format('d/m/Y H:i:s') : '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        @if($log->error_message)
            <div class="col-md-6">
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Mensagem de Erro</h3>
                    </div>
                    <div class="card-body">
                        <pre class="mb-0" style="white-space: pre-wrap; word-break: break-word;">{{ $log->error_message }}</pre>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Payload</h3>
        </div>
        <div class="card-body p-0">
            <pre class="m-0 p-3" style="background: #f4f6f9; max-height: 400px; overflow-y: auto;">{{ json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Cabeçalhos HTTP</h3>
        </div>
        <div class="card-body p-0">
            <pre class="m-0 p-3" style="background: #f4f6f9; max-height: 300px; overflow-y: auto;">{{ json_encode($log->headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>
@stop
