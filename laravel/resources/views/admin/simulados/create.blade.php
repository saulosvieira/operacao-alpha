@extends('adminlte::page')
@section('title', 'Novo Simulado')
@section('content_header')
    <h1>Novo Simulado</h1>
@stop
@section('content')
    <div class="card">
        <form action="{{ route('admin.simulados.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="carreira_id">Carreira *</label>
                    <select class="form-control @error('carreira_id') is-invalid @enderror" id="carreira_id" name="carreira_id" required>
                        <option value="">Selecione...</option>
                        @foreach($carreiras as $carreira)
                            <option value="{{ $carreira->id }}">{{ $carreira->nome }}</option>
                        @endforeach
                    </select>
                    @error('carreira_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="titulo">Título *</label>
                    <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo" value="{{ old('titulo') }}" required>
                    @error('titulo')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea class="form-control @error('descricao') is-invalid @enderror" id="descricao" name="descricao" rows="3">{{ old('descricao') }}</textarea>
                    @error('descricao')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="tempo_limite_minutos">Tempo Limite (minutos) *</label>
                    <input type="number" class="form-control @error('tempo_limite_minutos') is-invalid @enderror" id="tempo_limite_minutos" name="tempo_limite_minutos" value="{{ old('tempo_limite_minutos', 60) }}" min="1" max="300" required>
                    @error('tempo_limite_minutos')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="ativo" name="ativo" value="1" checked>
                        <label class="custom-control-label" for="ativo">Simulado Ativo</label>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                <a href="{{ route('admin.simulados.index') }}" class="btn btn-default"><i class="fas fa-times"></i> Cancelar</a>
            </div>
        </form>
    </div>
@stop
