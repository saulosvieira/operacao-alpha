@extends('adminlte::page')

@section('title', 'Carreiras')

@section('content_header')
    <h1>Carreiras</h1>
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
            <a href="{{ route('admin.carreiras.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nova Carreira
            </a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="80">ID</th>
                        <th>Nome</th>
                        <th>Slug</th>
                        <th width="120">Simulados</th>
                        <th width="100">Status</th>
                        <th width="150">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($carreiras as $carreira)
                        <tr>
                            <td>{{ $carreira->id }}</td>
                            <td>{{ $carreira->nome }}</td>
                            <td><code>{{ $carreira->slug }}</code></td>
                            <td class="text-center">
                                <span class="badge badge-info">{{ $carreira->simulados_count }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $carreira->ativa ? 'success' : 'danger' }}">
                                    {{ $carreira->ativa ? 'Ativa' : 'Inativa' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.carreiras.edit', $carreira) }}" 
                                   class="btn btn-sm btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.carreiras.destroy', $carreira) }}" 
                                      method="POST" 
                                      style="display: inline-block;"
                                      onsubmit="return confirm('Tem certeza que deseja excluir esta carreira?')">
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
                            <td colspan="6" class="text-center">Nenhuma carreira cadastrada</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($carreiras->hasPages())
            <div class="card-footer">
                {{ $carreiras->links() }}
            </div>
        @endif
    </div>
@stop
