@extends('adminlte::page')

@section('title', 'Novo Edital')

@section('content_header')
    <h1>Novo Edital</h1>
@stop

@section('content')
    <div class="card">
        <form action="{{ route('admin.notices.store') }}" method="POST">
            @csrf
            
            <div class="card-body">
                <div class="form-group">
                    <label for="career_id">Career *</label>
                    <select class="form-control @error('career_id') is-invalid @enderror" 
                            id="career_id" 
                            name="career_id" 
                            required>
                        <option value="">Select a career...</option>
                        @foreach($careers as $career)
                            <option value="{{ $career->id }}" {{ old('career_id') == $career->id ? 'selected' : '' }}>
                                {{ $career->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('career_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="title">Title *</label>
                    <input type="text" 
                           class="form-control @error('title') is-invalid @enderror" 
                           id="title" 
                           name="title" 
                           value="{{ old('title') }}" 
                           required>
                    @error('title')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" 
                              name="description" 
                              rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="exam_date">Exam Date</label>
                    <input type="date" 
                           class="form-control @error('exam_date') is-invalid @enderror" 
                           id="exam_date" 
                           name="exam_date" 
                           value="{{ old('exam_date') }}">
                    @error('exam_date')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" 
                               class="custom-control-input" 
                               id="active" 
                               name="active" 
                               value="1" 
                               {{ old('active', true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="active">Active Notice</label>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar
                </button>
                <a href="{{ route('admin.notices.index') }}" class="btn btn-default">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
@stop
