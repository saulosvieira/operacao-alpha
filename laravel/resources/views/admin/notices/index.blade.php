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
            <a href="{{ route('admin.notices.create') }}" class="btn btn-primary">
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
                    @forelse($notices as $notice)
                        <tr>
                            <td>{{ $notice->id }}</td>
                            <td>{{ $notice->title }}</td>
                            <td>{{ $notice->career?->name ?? '-' }}</td>
                            <td>{{ $notice->examDate ? \Carbon\Carbon::parse($notice->examDate)->format('d/m/Y') : '-' }}</td>
                            <td>
                                <span class="badge badge-{{ $notice->active ? 'success' : 'danger' }}">
                                    {{ $notice->active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.notices.edit', $notice->id) }}" 
                                   class="btn btn-sm btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.notices.destroy', $notice->id) }}" 
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
        @if($notices->hasPages())
            <div class="card-footer">
                {{ $notices->links() }}
            </div>
        @endif
    </div>
@stop
