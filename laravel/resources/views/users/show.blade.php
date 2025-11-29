@extends('adminlte::page')

@section('title', 'Visualizar Usuário')

@section('content_header')
    <h1>Visualizar Usuário</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong>ID:</strong></label>
                        <p>{{ $user->id }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong>Perfil:</strong></label>
                        <p>
                            @if($user->role === 'admin')
                                <span class="badge badge-danger">Admin</span>
                            @elseif($user->role === 'consultor')
                                <span class="badge badge-info">Consultor</span>
                            @else
                                <span class="badge badge-secondary">Usuário</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong>Nome:</strong></label>
                        <p>{{ $user->name }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong>E-mail:</strong></label>
                        <p>{{ $user->email }}</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong>Telefone:</strong></label>
                        <p>{{ $user->phone ?? 'Não informado' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong>Cadastrado em:</strong></label>
                        <p>{{ $user->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            @if($user->subscription_status)
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong>Status Assinatura:</strong></label>
                        <p>
                            @if($user->subscription_status === 'active')
                                <span class="badge badge-success">Ativa</span>
                            @elseif($user->subscription_status === 'trial')
                                <span class="badge badge-info">Trial</span>
                            @elseif($user->subscription_status === 'expired')
                                <span class="badge badge-warning">Expirada</span>
                            @else
                                <span class="badge badge-secondary">Inativa</span>
                            @endif
                        </p>
                    </div>
                </div>
                @if($user->subscription_expires_at)
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong>Expira em:</strong></label>
                        <p>{{ $user->subscription_expires_at->format('d/m/Y') }}</p>
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>

        <div class="card-footer">
            <a href="{{ route('usuarios.edit', $user) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('usuarios.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
@stop
