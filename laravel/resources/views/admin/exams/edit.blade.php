@extends('adminlte::page')
@section('title', 'Editar Simulado')
@section('content_header')
    <h1>Editar Simulado</h1>
@stop
@section('content')
    <div class="card">
        <form action="{{ route('admin.exams.update', $exam->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label for="career_id">Carreira *</label>
                    <select class="form-control @error('career_id') is-invalid @enderror" id="career_id" name="career_id" required>
                        @foreach($careers as $career)
                            <option value="{{ $career->id }}" {{ old('career_id', $exam->careerId) == $career->id ? 'selected' : '' }}>{{ $career->name }}</option>
                        @endforeach
                    </select>
                    @error('career_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="title">Título *</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $exam->title) }}" required>
                    @error('title')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="description">Descrição</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $exam->description) }}</textarea>
                    @error('description')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="time_limit_minutes">Tempo Limite (minutos) *</label>
                    <input type="number" class="form-control @error('time_limit_minutes') is-invalid @enderror" id="time_limit_minutes" name="time_limit_minutes" value="{{ old('time_limit_minutes', $exam->timeLimitMinutes) }}" min="1" max="300" required>
                    @error('time_limit_minutes')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Modo de Feedback *</label>
                    <div class="@error('feedback_mode') is-invalid @enderror">
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="feedback_mode_final" name="feedback_mode" value="final" {{ old('feedback_mode', $exam->feedbackMode ?? 'final') === 'final' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="feedback_mode_final">
                                <strong>Ao Finalizar</strong> - Exibe resultados apenas ao concluir o simulado
                            </label>
                        </div>
                        <div class="custom-control custom-radio mt-2">
                            <input type="radio" class="custom-control-input" id="feedback_mode_immediate" name="feedback_mode" value="immediate" {{ old('feedback_mode', $exam->feedbackMode ?? 'final') === 'immediate' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="feedback_mode_immediate">
                                <strong>Imediato</strong> - Exibe resposta correta após cada questão
                            </label>
                        </div>
                    </div>
                    @error('feedback_mode')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="hidden" name="active" value="0">
                        <input type="checkbox" class="custom-control-input" id="active" name="active" value="1" {{ old('active', $exam->active) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="active">Simulado Ativo</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="hidden" name="is_free" value="0">
                        <input type="checkbox" class="custom-control-input" id="is_free" name="is_free" value="1" {{ old('is_free', $exam->isFree ?? false) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_free">Simulado Gratuito</label>
                    </div>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Este simulado possui <strong>{{ $exam->totalQuestions }}</strong> questão(ões) associada(s).
                    <a href="{{ route('admin.exams.questions.index', $exam->id) }}" class="btn btn-sm btn-info ml-3">
                        <i class="fas fa-list-ol"></i> Gerenciar Questões
                    </a>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Atualizar</button>
                <a href="{{ route('admin.exams.questions.index', $exam->id) }}" class="btn btn-info"><i class="fas fa-list-ol"></i> Gerenciar Questões</a>
                <a href="{{ route('admin.exams.index') }}" class="btn btn-default"><i class="fas fa-times"></i> Cancelar</a>
            </div>
        </form>
    </div>
@stop
