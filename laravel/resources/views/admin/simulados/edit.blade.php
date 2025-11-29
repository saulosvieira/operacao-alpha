@extends('adminlte::page')
@section('title', 'Editar Simulado')
@section('content_header')
    <h1>Editar Simulado</h1>
@stop
@section('content')
    <div class="card">
        <form action="{{ route('admin.simulados.update', $simulado) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label for="carreira_id">Carreira *</label>
                    <select class="form-control @error('carreira_id') is-invalid @enderror" id="carreira_id" name="carreira_id" required>
                        @foreach($carreiras as $carreira)
                            <option value="{{ $carreira->id }}" {{ old('carreira_id', $simulado->carreira_id) == $carreira->id ? 'selected' : '' }}>{{ $carreira->nome }}</option>
                        @endforeach
                    </select>
                    @error('carreira_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="titulo">Título *</label>
                    <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo" value="{{ old('titulo', $simulado->titulo) }}" required>
                    @error('titulo')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea class="form-control @error('descricao') is-invalid @enderror" id="descricao" name="descricao" rows="3">{{ old('descricao', $simulado->descricao) }}</textarea>
                    @error('descricao')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="tempo_limite_minutos">Tempo Limite (minutos) *</label>
                    <input type="number" class="form-control @error('tempo_limite_minutos') is-invalid @enderror" id="tempo_limite_minutos" name="tempo_limite_minutos" value="{{ old('tempo_limite_minutos', $simulado->tempo_limite_minutos) }}" min="1" max="300" required>
                    @error('tempo_limite_minutos')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="ativo" name="ativo" value="1" {{ old('ativo', $simulado->ativo) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="ativo">Simulado Ativo</label>
                    </div>
                </div>
                @if($simulado->questoes_count > 0)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Este simulado possui <strong>{{ $simulado->questoes_count }}</strong> questão(ões) associada(s).
                    </div>
                @endif
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Atualizar</button>
                <a href="{{ route('admin.simulados.index') }}" class="btn btn-default"><i class="fas fa-times"></i> Cancelar</a>
            </div>
        </form>
    </div>
@stop
