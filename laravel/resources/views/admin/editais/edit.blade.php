@extends('adminlte::page')

@section('title', 'Editar Edital')

@section('content_header')
    <h1>Editar Edital</h1>
@stop

@section('content')
    <div class="card">
        <form action="{{ route('admin.editais.update', $edital) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card-body">
                <div class="form-group">
                    <label for="carreira_id">Carreira *</label>
                    <select class="form-control @error('carreira_id') is-invalid @enderror" 
                            id="carreira_id" 
                            name="carreira_id" 
                            required>
                        <option value="">Selecione uma carreira...</option>
                        @foreach($carreiras as $carreira)
                            <option value="{{ $carreira->id }}" 
                                {{ old('carreira_id', $edital->carreira_id) == $carreira->id ? 'selected' : '' }}>
                                {{ $carreira->nome }}
                            </option>
                        @endforeach
                    </select>
                    @error('carreira_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="titulo">Título *</label>
                    <input type="text" 
                           class="form-control @error('titulo') is-invalid @enderror" 
                           id="titulo" 
                           name="titulo" 
                           value="{{ old('titulo', $edital->titulo) }}" 
                           required>
                    @error('titulo')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea class="form-control @error('descricao') is-invalid @enderror" 
                              id="descricao" 
                              name="descricao" 
                              rows="3">{{ old('descricao', $edital->descricao) }}</textarea>
                    @error('descricao')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="data_publicacao">Data de Publicação</label>
                    <input type="date" 
                           class="form-control @error('data_publicacao') is-invalid @enderror" 
                           id="data_publicacao" 
                           name="data_publicacao" 
                           value="{{ old('data_publicacao', $edital->data_publicacao?->format('Y-m-d')) }}">
                    @error('data_publicacao')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" 
                               class="custom-control-input" 
                               id="ativo" 
                               name="ativo" 
                               value="1" 
                               {{ old('ativo', $edital->ativo) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="ativo">Edital Ativo</label>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Atualizar
                </button>
                <a href="{{ route('admin.editais.index') }}" class="btn btn-default">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
@stop
