@extends('adminlte::page')

@section('title', 'Detalhe da Tentativa #' . $attempt->id)

@section('content_header')
    <h1>Detalhe da Tentativa <small class="text-muted">#{{ $attempt->id }}</small></h1>
@stop

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.attempts.index') }}" class="btn btn-default">
            <i class="fas fa-arrow-left"></i> Voltar à listagem
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- Card de Informações Resumidas --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-info-circle"></i> Informações da Tentativa</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <th width="140">Nome</th>
                            <td>{{ $attempt->userName }}</td>
                        </tr>
                        <tr>
                            <th>E-mail</th>
                            <td>{{ $attempt->userEmail }}</td>
                        </tr>
                        <tr>
                            <th>Simulado</th>
                            <td>{{ $attempt->examTitle }}</td>
                        </tr>
                        <tr>
                            <th>Carreira</th>
                            <td>{{ $attempt->careerName }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <th width="140">Nota</th>
                            <td><span class="badge badge-primary" style="font-size: 1em;">{{ number_format($attempt->score, 1) }}</span></td>
                        </tr>
                        <tr>
                            <th>Acertos</th>
                            <td>{{ $attempt->correctAnswers }}/{{ $attempt->totalQuestions }}</td>
                        </tr>
                        <tr>
                            <th>Duração</th>
                            <td>{{ $attempt->durationMinutes }} min</td>
                        </tr>
                        <tr>
                            <th>Finalização</th>
                            <td>{{ \Carbon\Carbon::parse($attempt->finishedAt)->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabela de Respostas --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list-ol"></i> Respostas</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Enunciado</th>
                        <th width="90">Selecionada</th>
                        <th width="80">Correta</th>
                        <th width="100">Resultado</th>
                        <th width="120">Taxa de Acerto</th>
                        <th width="110">Reclamação</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($answers as $answer)
                        <tr class="{{ !$answer->isCorrect ? 'table-danger' : '' }}"
                            style="cursor: pointer;"
                            data-toggle="collapse"
                            data-target="#answer-detail-{{ $answer->questionId }}"
                            aria-expanded="false"
                            aria-controls="answer-detail-{{ $answer->questionId }}">
                            <td>{{ $answer->questionNumber }}</td>
                            <td>{{ Str::limit($answer->statement, 80) }}</td>
                            <td class="text-center"><span class="badge badge-secondary">{{ strtoupper($answer->selectedOption) }}</span></td>
                            <td class="text-center"><span class="badge badge-info">{{ strtoupper($answer->correctAnswer) }}</span></td>
                            <td class="text-center">
                                @if($answer->isCorrect)
                                    <span class="badge badge-success">Correto</span>
                                @else
                                    <span class="badge badge-danger">Incorreto</span>
                                @endif
                            </td>
                            <td class="text-center">
                                {{ number_format($answer->accuracyRate, 1) }}%
                                @if($answer->accuracyRate < 30)
                                    <br><span class="badge badge-warning" title="Taxa de acerto inferior a 30%">Potencialmente problemática</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($answer->hasPendingComplaints)
                                    <span class="badge badge-warning" title="Reclamação pendente">
                                        <i class="fas fa-exclamation-triangle"></i> Pendente
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        {{-- Seção Expansível --}}
                        <tr class="collapse" id="answer-detail-{{ $answer->questionId }}">
                            <td colspan="7" class="p-0">
                                <div class="p-3 bg-light">
                                    <h6><strong>Enunciado Completo</strong></h6>
                                    <p>{{ $answer->statement }}</p>

                                    <h6 class="mt-3"><strong>Alternativas</strong></h6>
                                    <ul class="list-unstyled mb-3">
                                        @foreach(['a' => $answer->optionA, 'b' => $answer->optionB, 'c' => $answer->optionC, 'd' => $answer->optionD, 'e' => $answer->optionE] as $letter => $text)
                                            <li class="p-2 mb-1 rounded {{ strtolower($answer->correctAnswer) === $letter ? 'bg-success text-white' : '' }}">
                                                <strong>{{ strtoupper($letter) }})</strong> {{ $text }}
                                                @if(strtolower($answer->correctAnswer) === $letter)
                                                    <i class="fas fa-check ml-1"></i>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>

                                    @if($answer->explanation)
                                        <h6><strong>Explicação</strong></h6>
                                        <p class="text-muted">{{ $answer->explanation }}</p>
                                    @endif

                                    <div class="mt-3 d-flex align-items-center" style="gap: 8px;">
                                        <button type="button" class="btn btn-warning btn-sm"
                                                data-toggle="modal"
                                                data-target="#complaintModal"
                                                data-question-id="{{ $answer->questionId }}"
                                                data-question-number="{{ $answer->questionNumber }}">
                                            <i class="fas fa-flag"></i> Registrar Reclamação
                                        </button>

                                        @if($answer->hasPendingComplaints)
                                            <a href="{{ route('admin.exams.questions.edit', [$attempt->examId, $answer->questionId]) }}"
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit"></i> Editar Questão
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Nenhuma resposta encontrada</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal de Reclamação --}}
    <div class="modal fade" id="complaintModal" tabindex="-1" role="dialog" aria-labelledby="complaintModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.complaints.store') }}">
                    @csrf
                    <input type="hidden" name="question_id" id="complaint-question-id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="complaintModalLabel">
                            <i class="fas fa-flag"></i> Registrar Reclamação — Questão <span id="complaint-question-number"></span>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="complaint-type">Tipo do Problema</label>
                            <select name="type" id="complaint-type" class="form-control" required>
                                <option value="">Selecione...</option>
                                <option value="incorrect_answer">Gabarito incorreto</option>
                                <option value="ambiguous_statement">Enunciado ambíguo</option>
                                <option value="outdated_question">Questão desatualizada</option>
                                <option value="formatting_error">Erro de formatação</option>
                                <option value="other">Outro</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="complaint-description">Descrição</label>
                            <textarea name="description" id="complaint-description" class="form-control" rows="4" required
                                      placeholder="Descreva o problema encontrado..."></textarea>
                        </div>

                        <div class="form-group">
                            <label for="complaint-priority">Prioridade</label>
                            <select name="priority" id="complaint-priority" class="form-control" required>
                                <option value="">Selecione...</option>
                                <option value="low">Baixa</option>
                                <option value="medium" selected>Média</option>
                                <option value="high">Alta</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-flag"></i> Registrar Reclamação
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
$(document).ready(function() {
    $('#complaintModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var questionId = button.data('question-id');
        var questionNumber = button.data('question-number');

        $(this).find('#complaint-question-id').val(questionId);
        $(this).find('#complaint-question-number').text('#' + questionNumber);
    });
});
</script>
@stop
