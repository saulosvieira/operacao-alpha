@extends('adminlte::page')

@section('title', 'Editais')

@section('content_header')
    <h1>Editais</h1>
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
            <a href="{{ route('admin.editais.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Novo Edital
            </a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="80">ID</th>
                        <th>Título</th>
                        <th width="200">Carreira</th>
                        <th width="120">Data Publicação</th>
                        <th width="100">Status</th>
                        <th width="150">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($editais as $edital)
                        <tr>
                            <td>{{ $edital->id }}</td>
                            <td>{{ $edital->titulo }}</td>
                            <td>{{ $edital->carreira->nome }}</td>
                            <td>{{ $edital->data_publicacao ? $edital->data_publicacao->format('d/m/Y') : '-' }}</td>
                            <td>
                                <span class="badge badge-{{ $edital->ativo ? 'success' : 'danger' }}">
                                    {{ $edital->ativo ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.editais.edit', $edital) }}" 
                                   class="btn btn-sm btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.editais.destroy', $edital) }}" 
                                      method="POST" 
                                      style="display: inline-block;"
                                      onsubmit="return confirm('Tem certeza que deseja excluir este edital?')">
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
                            <td colspan="6" class="text-center">Nenhum edital cadastrado</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($editais->hasPages())
            <div class="card-footer">
                {{ $editais->links() }}
            </div>
        @endif
    </div>
@stop
