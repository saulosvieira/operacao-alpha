@extends('adminlte::page')
@section('title', 'Nova Questão - ' . $exam->title)
@section('content_header')
    <h1>Nova Questão</h1>
    <small class="text-muted">Simulado: {{ $exam->title }}</small>
@stop
@section('content')
    <div class="card">
        <form action="{{ route('admin.exams.questions.store', $exam) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                {{-- Question Number --}}
                <div class="form-group">
                    <label for="question_number">Número da Questão *</label>
                    <input type="number" 
                           class="form-control @error('question_number') is-invalid @enderror" 
                           id="question_number" 
                           name="question_number" 
                           value="{{ old('question_number') }}" 
                           min="1" 
                           required>
                    @error('question_number')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Statement --}}
                <div class="form-group">
                    <label for="statement">Enunciado *</label>
                    <textarea class="form-control @error('statement') is-invalid @enderror" 
                              id="statement" 
                              name="statement" 
                              rows="4" 
                              required>{{ old('statement') }}</textarea>
                    @error('statement')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Statement Image --}}
                <div class="form-group">
                    <label for="statement_image">Imagem do Enunciado (opcional)</label>
                    <div class="custom-file">
                        <input type="file" 
                               class="custom-file-input @error('statement_image') is-invalid @enderror" 
                               id="statement_image" 
                               name="statement_image"
                               accept="image/*"
                               onchange="previewImage(this, 'statement_image_preview')">
                        <label class="custom-file-label" for="statement_image">Escolher arquivo...</label>
                        @error('statement_image')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <img id="statement_image_preview" src="#" alt="Preview" class="img-thumbnail mt-2" style="max-height: 200px; display: none;">
                </div>

                <hr>
                <h5>Alternativas</h5>

                {{-- Options A-E --}}
                @foreach(['A', 'B', 'C', 'D', 'E'] as $letter)
                    @php $lowerLetter = strtolower($letter); @endphp
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <strong>Alternativa {{ $letter }}</strong>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="option_{{ $lowerLetter }}">Texto *</label>
                                <textarea class="form-control @error('option_' . $lowerLetter) is-invalid @enderror" 
                                          id="option_{{ $lowerLetter }}" 
                                          name="option_{{ $lowerLetter }}" 
                                          rows="2" 
                                          required>{{ old('option_' . $lowerLetter) }}</textarea>
                                @error('option_' . $lowerLetter)<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group mb-0">
                                <label for="option_{{ $lowerLetter }}_image">Imagem (opcional)</label>
                                <div class="custom-file">
                                    <input type="file" 
                                           class="custom-file-input @error('option_' . $lowerLetter . '_image') is-invalid @enderror" 
                                           id="option_{{ $lowerLetter }}_image" 
                                           name="option_{{ $lowerLetter }}_image"
                                           accept="image/*"
                                           onchange="previewImage(this, 'option_{{ $lowerLetter }}_image_preview')">
                                    <label class="custom-file-label" for="option_{{ $lowerLetter }}_image">Escolher arquivo...</label>
                                    @error('option_' . $lowerLetter . '_image')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                                <img id="option_{{ $lowerLetter }}_image_preview" src="#" alt="Preview" class="img-thumbnail mt-2" style="max-height: 150px; display: none;">
                            </div>
                        </div>
                    </div>
                @endforeach

                <hr>

                {{-- Correct Answer --}}
                <div class="form-group">
                    <label>Resposta Correta *</label>
                    <div class="d-flex">
                        @foreach(['A', 'B', 'C', 'D', 'E'] as $letter)
                            <div class="custom-control custom-radio mr-4">
                                <input type="radio" 
                                       class="custom-control-input @error('correct_answer') is-invalid @enderror" 
                                       id="correct_{{ $letter }}" 
                                       name="correct_answer" 
                                       value="{{ $letter }}"
                                       {{ old('correct_answer') == $letter ? 'checked' : '' }}
                                       required>
                                <label class="custom-control-label" for="correct_{{ $letter }}">{{ $letter }}</label>
                            </div>
                        @endforeach
                    </div>
                    @error('correct_answer')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>

                {{-- Explanation --}}
                <div class="form-group">
                    <label for="explanation">Explicação (opcional)</label>
                    <textarea class="form-control @error('explanation') is-invalid @enderror" 
                              id="explanation" 
                              name="explanation" 
                              rows="3">{{ old('explanation') }}</textarea>
                    @error('explanation')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    <small class="text-muted">Explicação exibida após o usuário responder a questão.</small>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                <a href="{{ route('admin.exams.questions.index', $exam) }}" class="btn btn-default"><i class="fas fa-times"></i> Cancelar</a>
            </div>
        </form>
    </div>
@stop

@section('js')
<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
        
        // Update label with filename
        const label = input.nextElementSibling;
        label.textContent = input.files[0].name;
    }
}

// Initialize custom file inputs
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.custom-file-input').forEach(function(input) {
        input.addEventListener('change', function() {
            const fileName = this.files[0] ? this.files[0].name : 'Escolher arquivo...';
            this.nextElementSibling.textContent = fileName;
        });
    });
});
</script>
@stop
