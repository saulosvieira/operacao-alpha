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
                    <label for="career_id">Career *</label>
                    <select class="form-control @error('career_id') is-invalid @enderror" id="career_id" name="career_id" required>
                        @foreach($careers as $career)
                            <option value="{{ $career->id }}" {{ old('career_id', $exam->careerId) == $career->id ? 'selected' : '' }}>{{ $career->name }}</option>
                        @endforeach
                    </select>
                    @error('career_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="title">Title *</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $exam->title) }}" required>
                    @error('title')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $exam->description) }}</textarea>
                    @error('description')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="time_limit_minutes">Time Limit (minutes) *</label>
                    <input type="number" class="form-control @error('time_limit_minutes') is-invalid @enderror" id="time_limit_minutes" name="time_limit_minutes" value="{{ old('time_limit_minutes', $exam->timeLimitMinutes) }}" min="1" max="300" required>
                    @error('time_limit_minutes')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="active" name="active" value="1" {{ old('active', $exam->active) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="active">Active Exam</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_free" name="is_free" value="1" {{ old('is_free', $exam->isFree ?? false) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_free">Simulado Gratuito</label>
                    </div>
                </div>
                @if($exam->totalQuestions > 0)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> This exam has <strong>{{ $exam->totalQuestions }}</strong> associated question(s).
                    </div>
                @endif
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Atualizar</button>
                <a href="{{ route('admin.exams.index') }}" class="btn btn-default"><i class="fas fa-times"></i> Cancelar</a>
            </div>
        </form>
    </div>
@stop
