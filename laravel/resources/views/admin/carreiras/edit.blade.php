@extends('adminlte::page')

@section('title', 'Editar Carreira')

@section('content_header')
    <h1>Editar Carreira</h1>
@stop

@section('content')
    <div class="card">
        <form action="{{ route('admin.carreiras.update', $carreira) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card-body">
                <div class="form-group">
                    <label for="nome">Nome *</label>
                    <input type="text" 
                           class="form-control @error('nome') is-invalid @enderror" 
                           id="nome" 
                           name="nome" 
                           value="{{ old('nome', $carreira->nome) }}" 
                           required>
                    @error('nome')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">
                        Slug atual: <code>{{ $carreira->slug }}</code>
                    </small>
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea class="form-control @error('descricao') is-invalid @enderror" 
                              id="descricao" 
                              name="descricao" 
                              rows="3">{{ old('descricao', $carreira->descricao) }}</textarea>
                    @error('descricao')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" 
                               class="custom-control-input" 
                               id="ativa" 
                               name="ativa" 
                               value="1" 
                               {{ old('ativa', $carreira->ativa) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="ativa">Carreira Ativa</label>
                    </div>
                </div>

                @if($carreira->simulados_count > 0)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Esta carreira possui <strong>{{ $carreira->simulados_count }}</strong> simulado(s) associado(s).
                    </div>
                @endif
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Atualizar
                </button>
                <a href="{{ route('admin.carreiras.index') }}" class="btn btn-default">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
@stop
