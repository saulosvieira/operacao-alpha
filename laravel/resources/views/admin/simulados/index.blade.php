@extends('adminlte::page')
@section('title', 'Simulados')
@section('content_header')
    <h1>Simulados</h1>
@stop
@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    <div class="card">
        <div class="card-header">
            <a href="{{ route('admin.simulados.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Novo Simulado
            </a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="80">ID</th>
                        <th>Título</th>
                        <th width="180">Carreira</th>
                        <th width="100">Tempo</th>
                        <th width="100">Questões</th>
                        <th width="100">Status</th>
                        <th width="150">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($simulados as $simulado)
                        <tr>
                            <td>{{ $simulado->id }}</td>
                            <td>{{ $simulado->titulo }}</td>
                            <td>{{ $simulado->carreira->nome }}</td>
                            <td>{{ $simulado->tempo_limite_minutos }} min</td>
                            <td class="text-center">
                                <span class="badge badge-info">{{ $simulado->questoes_count }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $simulado->ativo ? 'success' : 'danger' }}">
                                    {{ $simulado->ativo ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.simulados.edit', $simulado) }}" 
                                   class="btn btn-sm btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.simulados.destroy', $simulado) }}" 
                                      method="POST" 
                                      style="display: inline-block;"
                                      onsubmit="return confirm('Tem certeza que deseja excluir este simulado?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Nenhum simulado cadastrado</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($simulados->hasPages())
            <div class="card-footer">
                {{ $simulados->links() }}
            </div>
        @endif
    </div>
@stop
