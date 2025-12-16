@extends('adminlte::page')

@section('title', 'Editar Carreira')

@section('content_header')
    <h1>Editar Carreira</h1>
@stop

@section('content')
    <div class="card">
        <form action="{{ route('admin.careers.update', $career->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Name *</label>
                    <input type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $career->name) }}" 
                           required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">
                        Current slug: <code>{{ $career->slug }}</code>
                    </small>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" 
                              name="description" 
                              rows="3">{{ old('description', $career->description) }}</textarea>
                    @error('description')
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
                               {{ old('active', $career->active) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="active">Active Career</label>
                    </div>
                </div>

                @if($career->examsCount > 0)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        This career has <strong>{{ $career->examsCount }}</strong> associated exam(s).
                    </div>
                @endif
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Atualizar
                </button>
                <a href="{{ route('admin.careers.index') }}" class="btn btn-default">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
@stop
