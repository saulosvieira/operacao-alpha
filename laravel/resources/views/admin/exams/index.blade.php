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
            <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">
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
                        <th width="100">Tipo</th>
                        <th width="150">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                        <tr>
                            <td>{{ $exam->id }}</td>
                            <td>{{ $exam->title }}</td>
                            <td>{{ $exam->career?->name ?? '-' }}</td>
                            <td>{{ $exam->timeLimitMinutes }} min</td>
                            <td class="text-center">
                                <span class="badge badge-info">{{ $exam->totalQuestions }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $exam->active ? 'success' : 'danger' }}">
                                    {{ $exam->active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $exam->is_free ? 'success' : 'warning' }}">
                                    {{ $exam->is_free ? 'Gratuito' : 'Premium' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.exams.edit', $exam->id) }}" 
                                   class="btn btn-sm btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.exams.destroy', $exam->id) }}" 
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
                            <td colspan="8" class="text-center">Nenhum simulado cadastrado</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($exams->hasPages())
            <div class="card-footer">
                {{ $exams->links() }}
            </div>
        @endif
    </div>
@stop
