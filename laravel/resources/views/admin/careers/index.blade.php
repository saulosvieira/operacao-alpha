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
            <a href="{{ route('admin.careers.create') }}" class="btn btn-primary">
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
                    @forelse($careers as $career)
                        <tr>
                            <td>{{ $career->id }}</td>
                            <td>{{ $career->name }}</td>
                            <td><code>{{ $career->slug }}</code></td>
                            <td class="text-center">
                                <span class="badge badge-info">{{ $career->examsCount }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $career->active ? 'success' : 'danger' }}">
                                    {{ $career->active ? 'Ativa' : 'Inativa' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.careers.edit', $career->id) }}" 
                                   class="btn btn-sm btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.careers.destroy', $career->id) }}" 
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
        @if($careers->hasPages())
            <div class="card-footer">
                {{ $careers->links() }}
            </div>
        @endif
    </div>
@stop
