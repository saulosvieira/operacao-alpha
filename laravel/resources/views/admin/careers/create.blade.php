@extends('adminlte::page')

@section('title', 'Nova Carreira')

@section('content_header')
    <h1>Nova Carreira</h1>
@stop

@section('content')
    <div class="card">
        <form action="{{ route('admin.careers.store') }}" method="POST">
            @csrf
            
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Name *</label>
                    <input type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">
                        The slug will be generated automatically from the name
                    </small>
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
                    <div class="custom-control custom-switch">
                        <input type="checkbox" 
                               class="custom-control-input" 
                               id="active" 
                               name="active" 
                               value="1" 
                               {{ old('active', true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="active">Active Career</label>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar
                </button>
                <a href="{{ route('admin.careers.index') }}" class="btn btn-default">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
@stop
