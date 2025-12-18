@extends('adminlte::page')
@section('title', 'Questões - ' . $exam->title)
@section('content_header')
    <h1>Questões do Simulado: {{ $exam->title }}</h1>
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
            <a href="{{ route('admin.exams.questions.create', $exam) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nova Questão
            </a>
            <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-secondary ml-2">
                <i class="fas fa-arrow-left"></i> Voltar ao Simulado
            </a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="80">Nº</th>
                        <th>Enunciado</th>
                        <th width="100">Resposta</th>
                        <th width="100">Imagem</th>
                        <th width="150">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($questions as $question)
                        <tr>
                            <td class="text-center">{{ $question->question_number }}</td>
                            <td>{{ Str::limit($question->statement, 100) }}</td>
                            <td class="text-center">
                                <span class="badge badge-success">{{ $question->correct_answer }}</span>
                            </td>
                            <td class="text-center">
                                @if($question->statement_image)
                                    <span class="badge badge-info"><i class="fas fa-image"></i> Sim</span>
                                @else
                                    <span class="badge badge-secondary">Não</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.exams.questions.edit', [$exam, $question]) }}" 
                                   class="btn btn-sm btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.exams.questions.destroy', [$exam, $question]) }}" 
                                      method="POST" 
                                      style="display: inline-block;"
                                      onsubmit="return confirm('Tem certeza que deseja excluir esta questão?')">
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
                            <td colspan="5" class="text-center">Nenhuma questão cadastrada</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <span class="text-muted">Total: {{ $questions->count() }} questão(ões)</span>
        </div>
    </div>
@stop
