@extends('adminlte::page')

@section('title', 'Nova Carreira')

@section('content_header')
    <h1>Nova Carreira</h1>
@stop

@section('content')
    <div class="card">
        <form action="{{ route('admin.carreiras.store') }}" method="POST">
            @csrf
            
            <div class="card-body">
                <div class="form-group">
                    <label for="nome">Nome *</label>
                    <input type="text" 
                           class="form-control @error('nome') is-invalid @enderror" 
                           id="nome" 
                           name="nome" 
                           value="{{ old('nome') }}" 
                           required>
                    @error('nome')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">
                        O slug será gerado automaticamente a partir do nome
                    </small>
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea class="form-control @error('descricao') is-invalid @enderror" 
                              id="descricao" 
                              name="descricao" 
                              rows="3">{{ old('descricao') }}</textarea>
                    @error('descricao')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" 
                               class="custom-control-input" 
                               id="ativa" 
                               name="ativa" 
                               value="1" 
                               {{ old('ativa', true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="ativa">Carreira Ativa</label>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar
                </button>
                <a href="{{ route('admin.carreiras.index') }}" class="btn btn-default">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
@stop
