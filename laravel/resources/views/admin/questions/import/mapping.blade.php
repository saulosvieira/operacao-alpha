@extends('adminlte::page')

@section('title', 'Mapeamento de Carreiras - Importação de Questões')

@section('content_header')
    <h1>Mapeamento de Carreiras</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.import.questions.index') }}">Importação</a></li>
        <li class="breadcrumb-item active">Mapeamento de Carreiras</li>
    </ol>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Progress Steps -->
            <div class="card">
                <div class="card-body">
                    <div class="progress-steps">
                        <div class="step completed">
                            <i class="fas fa-upload"></i>
                            <span>Enviar Arquivo</span>
                        </div>
                        <div class="step active">
                            <i class="fas fa-map-marked-alt"></i>
                            <span>Mapear Carreiras</span>
                        </div>
                        <div class="step">
                            <i class="fas fa-eye"></i>
                            <span>Pré-visualização</span>
                        </div>
                        <div class="step">
                            <i class="fas fa-play"></i>
                            <span>Importar</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Session Info -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i>
                        Informações da Sessão de Importação
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Arquivo:</strong> {{ $session->filename }}<br>
                            <strong>Total de Linhas:</strong> {{ number_format($session->total_rows) }}<br>
                            <strong>Status:</strong> 
                            <span class="badge badge-info">{{ ucfirst($session->status) }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Criado em:</strong> {{ $session->created_at->format('Y-m-d H:i:s') }}<br>
                            <strong>Expira em:</strong> {{ $session->expires_at->format('Y-m-d H:i:s') }}<br>
                            <strong>Criado por:</strong> {{ $session->creator->name }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Career Mapping Form -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-map-marked-alt"></i>
                        Mapear Abreviações de Carreiras
                    </h3>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Instruções:</strong> Mapeie cada abreviação de carreira encontrada no seu arquivo Excel para a carreira correspondente no sistema. 
                        Todas as abreviações devem ser mapeadas antes de prosseguir para a pré-visualização.
                    </div>

                    <form action="{{ route('admin.import.sessions.mapping.process', $session->id) }}" method="POST" id="mapping-form">
                        @csrf
                        
                        @if (count($abbreviations) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="30%">Abreviação de Carreira</th>
                                            <th width="50%">Mapear para Carreira</th>
                                            <th width="20%">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($abbreviations as $abbreviation)
                                            <tr>
                                                <td>
                                                    <strong>{{ $abbreviation }}</strong>
                                                    <br>
                                                    <small class="text-muted">Encontrado no arquivo Excel</small>
                                                </td>
                                                <td>
                                                    <select name="mappings[{{ $abbreviation }}]" 
                                                            class="form-control career-select @error('mappings.' . $abbreviation) is-invalid @enderror" 
                                                            required>
                                                        <option value="">-- Selecione uma Carreira --</option>
                                                        @foreach ($careers as $career)
                                                            <option value="{{ $career->id }}" 
                                                                    {{ old('mappings.' . $abbreviation) == $career->id ? 'selected' : '' }}>
                                                                {{ $career->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('mappings.' . $abbreviation)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-info auto-suggest" 
                                                            data-abbreviation="{{ $abbreviation }}">
                                                        <i class="fas fa-magic"></i>
                                                        Sugerir automaticamente
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="form-group mt-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-secondary" onclick="history.back()">
                                            <i class="fas fa-arrow-left"></i>
                                            Voltar ao Envio
                                        </button>
                                        <button type="button" class="btn btn-warning ml-2" onclick="cancelImport()">
                                            <i class="fas fa-times"></i>
                                            Cancelar Importação
                                        </button>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button type="submit" class="btn btn-primary" id="submit-btn">
                                            <i class="fas fa-arrow-right"></i>
                                            Continuar para Pré-visualização
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Nenhuma abreviação de carreira encontrada no arquivo Excel. Por favor, verifique o formato do seu arquivo.
                            </div>
                            <a href="{{ route('admin.import.questions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                Voltar ao Envio
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.progress-steps {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 20px 0;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    position: relative;
    color: #6c757d;
}

.step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 15px;
    left: 60%;
    width: 80%;
    height: 2px;
    background-color: #dee2e6;
    z-index: -1;
}

.step.completed {
    color: #28a745;
}

.step.completed:not(:last-child)::after {
    background-color: #28a745;
}

.step.active {
    color: #007bff;
    font-weight: bold;
}

.step i {
    font-size: 24px;
    margin-bottom: 5px;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: currentColor;
    color: white;
}

.step.completed i {
    background-color: #28a745;
}

.step.active i {
    background-color: #007bff;
}

.step span {
    font-size: 12px;
    text-align: center;
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Auto-suggest functionality
    $('.auto-suggest').on('click', function() {
        const abbreviation = $(this).data('abbreviation');
        const select = $(this).closest('tr').find('.career-select');
        
        // Simple fuzzy matching logic
        const careers = @json($careers->pluck('name', 'id'));
        let bestMatch = null;
        let bestScore = 0;
        
        Object.entries(careers).forEach(([id, name]) => {
            const score = calculateSimilarity(abbreviation.toLowerCase(), name.toLowerCase());
            if (score > bestScore) {
                bestScore = score;
                bestMatch = id;
            }
        });
        
        if (bestMatch && bestScore > 0.3) {
            select.val(bestMatch);
            select.trigger('change');
            
            // Highlight the suggestion
            select.addClass('border-success');
            setTimeout(() => {
                select.removeClass('border-success');
            }, 2000);
        } else {
            alert('Nenhuma correspondência adequada encontrada para "' + abbreviation + '". Por favor, selecione manualmente.');
        }
    });
    
    // Form validation
    $('#mapping-form').on('submit', function(e) {
        let hasEmptyMappings = false;
        $('.career-select').each(function() {
            if (!$(this).val()) {
                hasEmptyMappings = true;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (hasEmptyMappings) {
            e.preventDefault();
            alert('Por favor, mapeie todas as abreviações de carreiras antes de continuar.');
            return false;
        }
        
        $('#submit-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processando...');
    });
    
    // Clear validation errors on change
    $('.career-select').on('change', function() {
        $(this).removeClass('is-invalid');
    });
});

function calculateSimilarity(str1, str2) {
    // Simple similarity calculation
    if (str1.includes(str2) || str2.includes(str1)) {
        return 0.8;
    }
    
    // Check for common abbreviations
    const commonAbbreviations = {
        'pm': 'polícia militar',
        'pc': 'polícia civil',
        'pf': 'polícia federal',
        'prf': 'polícia rodoviária federal',
        'cbm': 'corpo de bombeiros militar',
        'tj': 'tribunal de justiça',
        'trf': 'tribunal regional federal',
        'trt': 'tribunal regional do trabalho',
        'tcu': 'tribunal de contas da união',
        'tce': 'tribunal de contas do estado'
    };
    
    const abbrev = str1.toLowerCase();
    if (commonAbbreviations[abbrev] && str2.includes(commonAbbreviations[abbrev])) {
        return 0.9;
    }
    
    // Basic string similarity
    let matches = 0;
    const minLength = Math.min(str1.length, str2.length);
    for (let i = 0; i < minLength; i++) {
        if (str1[i] === str2[i]) {
            matches++;
        }
    }
    
    return matches / Math.max(str1.length, str2.length);
}

function cancelImport() {
    if (confirm('Tem certeza que deseja cancelar esta importação? Todo o progresso será perdido.')) {
        window.location.href = '{{ route("admin.import.sessions.cancel", $session->id) }}';
    }
}
</script>
@stop