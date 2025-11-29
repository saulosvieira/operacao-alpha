# Exemplos de Código - MVP Simulados

## Controllers

### CarreiraController (Admin)

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carreira;
use App\Http\Requests\CarreiraRequest;
use Illuminate\Support\Str;

class CarreiraController extends Controller
{
    public function index()
    {
        $carreiras = Carreira::withCount('simulados')
            ->orderBy('nome')
            ->paginate(15);
            
        return view('admin.carreiras.index', compact('carreiras'));
    }
    
    public function create()
    {
        return view('admin.carreiras.create');
    }
    
    public function store(CarreiraRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['nome']);
        
        Carreira::create($data);
        
        return redirect()
            ->route('admin.carreiras.index')
            ->with('success', 'Carreira criada com sucesso!');
    }
    
    public function edit(Carreira $carreira)
    {
        return view('admin.carreiras.edit', compact('carreira'));
    }
    
    public function update(CarreiraRequest $request, Carreira $carreira)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['nome']);
        
        $carreira->update($data);
        
        return redirect()
            ->route('admin.carreiras.index')
            ->with('success', 'Carreira atualizada com sucesso!');
    }
    
    public function destroy(Carreira $carreira)
    {
        if ($carreira->simulados()->count() > 0) {
            return back()->with('error', 'Não é possível excluir carreira com simulados associados.');
        }
        
        $carreira->delete();
        
        return redirect()
            ->route('admin.carreiras.index')
            ->with('success', 'Carreira excluída com sucesso!');
    }
}
```

### SimuladoPublicoController (Frontend)

```php
<?php

namespace App\Http\Controllers;

use App\Models\Simulado;
use App\Models\RespostaUsuario;
use App\Models\ResultadoSimulado;
use App\Services\SimuladoService;
use Illuminate\Http\Request;

class SimuladoPublicoController extends Controller
{
    public function __construct(
        private SimuladoService $simuladoService
    ) {}
    
    public function index()
    {
        $simulados = Simulado::where('ativo', true)
            ->with('carreira:id,nome')
            ->withCount('questoes')
            ->orderBy('titulo')
            ->paginate(12);
            
        return view('simulados.index', compact('simulados'));
    }
    
    public function show(Simulado $simulado)
    {
        $simulado->load('carreira', 'questoes');
        
        // Verificar se usuário já fez este simulado
        $jaFez = ResultadoSimulado::where('user_id', auth()->id())
            ->where('simulado_id', $simulado->id)
            ->exists();
            
        return view('simulados.show', compact('simulado', 'jaFez'));
    }
    
    public function iniciar(Simulado $simulado)
    {
        // Limpar respostas anteriores se existirem
        RespostaUsuario::where('user_id', auth()->id())
            ->where('simulado_id', $simulado->id)
            ->delete();
            
        $simulado->load('questoes');
        
        return view('simulados.realizar', compact('simulado'));
    }
    
    public function salvarResposta(Request $request, Simulado $simulado)
    {
        $request->validate([
            'questao_id' => 'required|exists:questoes,id',
            'resposta' => 'required|in:A,B,C,D,E',
            'tempo_resposta' => 'nullable|integer',
        ]);
        
        $questao = $simulado->questoes()->findOrFail($request->questao_id);
        $correta = $questao->resposta_correta === $request->resposta;
        
        RespostaUsuario::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'simulado_id' => $simulado->id,
                'questao_id' => $request->questao_id,
            ],
            [
                'resposta_escolhida' => $request->resposta,
                'correta' => $correta,
                'tempo_resposta_segundos' => $request->tempo_resposta,
            ]
        );
        
        return response()->json(['success' => true, 'correta' => $correta]);
    }
    
    public function finalizar(Request $request, Simulado $simulado)
    {
        $resultado = $this->simuladoService->finalizarSimulado(
            auth()->user(),
            $simulado,
            $request->tempo_total
        );
        
        return redirect()
            ->route('simulados.resultado', $resultado->id)
            ->with('success', 'Simulado finalizado!');
    }
    
    public function resultado(ResultadoSimulado $resultado)
    {
        // Verificar se o resultado pertence ao usuário
        if ($resultado->user_id !== auth()->id()) {
            abort(403);
        }
        
        $resultado->load([
            'simulado.questoes',
            'user.respostas' => function ($query) use ($resultado) {
                $query->where('simulado_id', $resultado->simulado_id);
            }
        ]);
        
        return view('simulados.resultado', compact('resultado'));
    }
    
    public function historico()
    {
        $resultados = ResultadoSimulado::where('user_id', auth()->id())
            ->with('simulado:id,titulo')
            ->orderBy('finalizado_em', 'desc')
            ->paginate(10);
            
        return view('simulados.historico', compact('resultados'));
    }
}
```

## Services

### SimuladoService

```php
<?php

namespace App\Services;

use App\Models\User;
use App\Models\Simulado;
use App\Models\ResultadoSimulado;
use App\Models\RespostaUsuario;

class SimuladoService
{
    public function finalizarSimulado(User $user, Simulado $simulado, int $tempoTotal): ResultadoSimulado
    {
        $respostas = RespostaUsuario::where('user_id', $user->id)
            ->where('simulado_id', $simulado->id)
            ->get();
            
        $totalQuestoes = $simulado->questoes()->count();
        $acertos = $respostas->where('correta', true)->count();
        $pontuacao = $totalQuestoes > 0 ? ($acertos / $totalQuestoes) * 100 : 0;
        
        $resultado = ResultadoSimulado::create([
            'user_id' => $user->id,
            'simulado_id' => $simulado->id,
            'pontuacao' => round($pontuacao, 2),
            'total_questoes' => $totalQuestoes,
            'acertos' => $acertos,
            'tempo_total_segundos' => $tempoTotal,
            'finalizado_em' => now(),
        ]);
        
        // Atualizar ranking
        app(RankingService::class)->atualizarRankingUsuario($user);
        
        return $resultado;
    }
    
    public function calcularEstatisticas(Simulado $simulado): array
    {
        $resultados = ResultadoSimulado::where('simulado_id', $simulado->id)->get();
        
        return [
            'total_realizacoes' => $resultados->count(),
            'media_pontuacao' => $resultados->avg('pontuacao'),
            'media_tempo' => $resultados->avg('tempo_total_segundos'),
            'melhor_pontuacao' => $resultados->max('pontuacao'),
            'pior_pontuacao' => $resultados->min('pontuacao'),
        ];
    }
}
```

### RankingService

```php
<?php

namespace App\Services;

use App\Models\User;
use App\Models\Ranking;
use App\Models\ResultadoSimulado;
use Carbon\Carbon;

class RankingService
{
    public function atualizarRankingUsuario(User $user): void
    {
        $hoje = Carbon::today();
        $inicioSemana = Carbon::now()->startOfWeek();
        
        // Calcular pontuação diária
        $pontuacaoDiaria = ResultadoSimulado::where('user_id', $user->id)
            ->whereDate('finalizado_em', $hoje)
            ->sum('pontuacao');
            
        // Calcular pontuação semanal
        $pontuacaoSemanal = ResultadoSimulado::where('user_id', $user->id)
            ->where('finalizado_em', '>=', $inicioSemana)
            ->sum('pontuacao');
            
        Ranking::updateOrCreate(
            [
                'user_id' => $user->id,
                'data_calculo' => $hoje,
            ],
            [
                'pontuacao_diaria' => $pontuacaoDiaria,
                'pontuacao_semanal' => $pontuacaoSemanal,
            ]
        );
    }
    
    public function obterRankingDiario(int $limite = 100): \Illuminate\Support\Collection
    {
        return Ranking::where('data_calculo', Carbon::today())
            ->with('user:id,name')
            ->orderBy('pontuacao_diaria', 'desc')
            ->limit($limite)
            ->get()
            ->map(function ($ranking, $index) {
                $ranking->posicao = $index + 1;
                return $ranking;
            });
    }
    
    public function obterRankingSemanal(int $limite = 100): \Illuminate\Support\Collection
    {
        return Ranking::where('data_calculo', Carbon::today())
            ->with('user:id,name')
            ->orderBy('pontuacao_semanal', 'desc')
            ->limit($limite)
            ->get()
            ->map(function ($ranking, $index) {
                $ranking->posicao = $index + 1;
                return $ranking;
            });
    }
}
```

### QuestaoImportService

```php
<?php

namespace App\Services;

use App\Models\Questao;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class QuestaoImportService
{
    public function importarCSV(string $caminhoArquivo, int $simuladoId): array
    {
        $erros = [];
        $sucessos = 0;
        $linha = 0;
        
        if (($handle = fopen($caminhoArquivo, 'r')) !== false) {
            // Pular cabeçalho
            fgetcsv($handle);
            
            while (($data = fgetcsv($handle)) !== false) {
                $linha++;
                
                try {
                    $this->validarLinha($data, $linha);
                    $this->criarQuestao($data, $simuladoId);
                    $sucessos++;
                } catch (\Exception $e) {
                    $erros[] = "Linha {$linha}: {$e->getMessage()}";
                }
            }
            
            fclose($handle);
        }
        
        return [
            'sucessos' => $sucessos,
            'erros' => $erros,
            'total_linhas' => $linha,
        ];
    }
    
    private function validarLinha(array $data, int $linha): void
    {
        if (count($data) < 16) {
            throw new \Exception("Número insuficiente de colunas");
        }
        
        $validator = Validator::make([
            'numero_questao' => $data[0],
            'enunciado' => $data[2],
            'alternativa_a' => $data[4],
            'alternativa_b' => $data[6],
            'alternativa_c' => $data[8],
            'alternativa_d' => $data[10],
            'alternativa_e' => $data[12],
            'resposta_correta' => $data[14],
        ], [
            'numero_questao' => 'required|integer',
            'enunciado' => 'required|string',
            'alternativa_a' => 'required|string',
            'alternativa_b' => 'required|string',
            'alternativa_c' => 'required|string',
            'alternativa_d' => 'required|string',
            'alternativa_e' => 'required|string',
            'resposta_correta' => 'required|in:A,B,C,D,E',
        ]);
        
        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }
    }
    
    private function criarQuestao(array $data, int $simuladoId): void
    {
        Questao::create([
            'simulado_id' => $simuladoId,
            'numero_questao' => $data[0],
            'enunciado' => $data[2],
            'imagem_enunciado' => $data[3] ?: null,
            'alternativa_a' => $data[4],
            'imagem_a' => $data[5] ?: null,
            'alternativa_b' => $data[6],
            'imagem_b' => $data[7] ?: null,
            'alternativa_c' => $data[8],
            'imagem_c' => $data[9] ?: null,
            'alternativa_d' => $data[10],
            'imagem_d' => $data[11] ?: null,
            'alternativa_e' => $data[12],
            'imagem_e' => $data[13] ?: null,
            'resposta_correta' => $data[14],
            'explicacao' => $data[15] ?: null,
        ]);
    }
}
```

## Middleware

### CheckSubscription

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        if (!$user->isSubscribed()) {
            return redirect()
                ->route('assinatura.upgrade')
                ->with('warning', 'Você precisa de uma assinatura ativa para acessar este conteúdo.');
        }
        
        return $next($request);
    }
}
```

## Views (Blade)

### admin/carreiras/index.blade.php

```blade
@extends('adminlte::page')

@section('title', 'Carreiras')

@section('content_header')
    <h1>Carreiras</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('admin.carreiras.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nova Carreira
            </a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Slug</th>
                        <th>Simulados</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($carreiras as $carreira)
                        <tr>
                            <td>{{ $carreira->id }}</td>
                            <td>{{ $carreira->nome }}</td>
                            <td>{{ $carreira->slug }}</td>
                            <td>{{ $carreira->simulados_count }}</td>
                            <td>
                                <span class="badge badge-{{ $carreira->ativa ? 'success' : 'danger' }}">
                                    {{ $carreira->ativa ? 'Ativa' : 'Inativa' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.carreiras.edit', $carreira) }}" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.carreiras.destroy', $carreira) }}" 
                                      method="POST" 
                                      style="display: inline-block;"
                                      onsubmit="return confirm('Tem certeza?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Nenhuma carreira cadastrada</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $carreiras->links() }}
        </div>
    </div>
@stop
```

### simulados/realizar.blade.php

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>{{ $simulado->titulo }}</h3>
                    <div id="cronometro" class="badge badge-danger badge-lg">
                        <i class="fas fa-clock"></i>
                        <span id="tempo-restante">{{ $simulado->tempo_limite_minutos }}:00</span>
                    </div>
                </div>
                <div class="card-body">
                    <div id="questao-container">
                        <!-- Questões serão carregadas aqui via JavaScript -->
                    </div>
                    
                    <div class="navegacao-questoes mt-4">
                        <button id="btn-anterior" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Anterior
                        </button>
                        <span id="contador-questoes" class="mx-3">
                            Questão <span id="questao-atual">1</span> de {{ $simulado->questoes->count() }}
                        </span>
                        <button id="btn-proxima" class="btn btn-primary">
                            Próxima <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                    
                    <div class="mt-4">
                        <button id="btn-finalizar" class="btn btn-success btn-lg btn-block">
                            <i class="fas fa-check"></i> Finalizar Simulado
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const simulado = @json($simulado);
    const tempoLimite = {{ $simulado->tempo_limite_minutos * 60 }};
    let tempoRestante = tempoLimite;
    let questaoAtual = 0;
    let respostas = {};
    
    // Cronômetro
    const cronometro = setInterval(() => {
        tempoRestante--;
        atualizarCronometro();
        
        if (tempoRestante <= 0) {
            clearInterval(cronometro);
            finalizarSimulado(true);
        }
    }, 1000);
    
    function atualizarCronometro() {
        const minutos = Math.floor(tempoRestante / 60);
        const segundos = tempoRestante % 60;
        document.getElementById('tempo-restante').textContent = 
            `${minutos.toString().padStart(2, '0')}:${segundos.toString().padStart(2, '0')}`;
    }
    
    // Carregar questão
    function carregarQuestao(index) {
        const questao = simulado.questoes[index];
        const container = document.getElementById('questao-container');
        
        let html = `
            <div class="questao">
                <h4>Questão ${questao.numero_questao}</h4>
                <p>${questao.enunciado}</p>
                ${questao.imagem_enunciado ? `<img src="/storage/questoes/${questao.imagem_enunciado}" class="img-fluid mb-3">` : ''}
                
                <div class="alternativas">
                    ${['a', 'b', 'c', 'd', 'e'].map(letra => `
                        <div class="form-check mb-3">
                            <input class="form-check-input" 
                                   type="radio" 
                                   name="resposta" 
                                   id="alt-${letra}" 
                                   value="${letra.toUpperCase()}"
                                   ${respostas[questao.id] === letra.toUpperCase() ? 'checked' : ''}>
                            <label class="form-check-label" for="alt-${letra}">
                                ${letra.toUpperCase()}) ${questao['alternativa_' + letra]}
                                ${questao['imagem_' + letra] ? `<br><img src="/storage/questoes/${questao['imagem_' + letra]}" class="img-fluid mt-2">` : ''}
                            </label>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
        
        container.innerHTML = html;
        
        // Event listener para salvar resposta
        document.querySelectorAll('input[name="resposta"]').forEach(input => {
            input.addEventListener('change', (e) => {
                salvarResposta(questao.id, e.target.value);
            });
        });
        
        questaoAtual = index;
        document.getElementById('questao-atual').textContent = index + 1;
    }
    
    // Salvar resposta
    function salvarResposta(questaoId, resposta) {
        respostas[questaoId] = resposta;
        
        fetch(`/simulados/{{ $simulado->id }}/salvar-resposta`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                questao_id: questaoId,
                resposta: resposta,
                tempo_resposta: tempoLimite - tempoRestante
            })
        });
    }
    
    // Navegação
    document.getElementById('btn-anterior').addEventListener('click', () => {
        if (questaoAtual > 0) {
            carregarQuestao(questaoAtual - 1);
        }
    });
    
    document.getElementById('btn-proxima').addEventListener('click', () => {
        if (questaoAtual < simulado.questoes.length - 1) {
            carregarQuestao(questaoAtual + 1);
        }
    });
    
    // Finalizar
    document.getElementById('btn-finalizar').addEventListener('click', () => {
        if (confirm('Tem certeza que deseja finalizar o simulado?')) {
            finalizarSimulado(false);
        }
    });
    
    function finalizarSimulado(automatico) {
        clearInterval(cronometro);
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/simulados/{{ $simulado->id }}/finalizar`;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        const tempoInput = document.createElement('input');
        tempoInput.type = 'hidden';
        tempoInput.name = 'tempo_total';
        tempoInput.value = tempoLimite - tempoRestante;
        form.appendChild(tempoInput);
        
        document.body.appendChild(form);
        form.submit();
    }
    
    // Carregar primeira questão
    carregarQuestao(0);
</script>
@endpush
@endsection
```

## JavaScript Components

### cronometro.js

```javascript
class Cronometro {
    constructor(tempoLimiteMinutos, onTempoEsgotado) {
        this.tempoRestante = tempoLimiteMinutos * 60;
        this.onTempoEsgotado = onTempoEsgotado;
        this.intervalo = null;
    }
    
    iniciar() {
        this.intervalo = setInterval(() => {
            this.tempoRestante--;
            this.atualizar();
            
            if (this.tempoRestante <= 0) {
                this.parar();
                this.onTempoEsgotado();
            }
        }, 1000);
    }
    
    parar() {
        if (this.intervalo) {
            clearInterval(this.intervalo);
            this.intervalo = null;
        }
    }
    
    atualizar() {
        const minutos = Math.floor(this.tempoRestante / 60);
        const segundos = this.tempoRestante % 60;
        const display = `${minutos.toString().padStart(2, '0')}:${segundos.toString().padStart(2, '0')}`;
        
        document.getElementById('tempo-restante').textContent = display;
        
        // Alerta visual quando faltam 5 minutos
        if (this.tempoRestante === 300) {
            document.getElementById('cronometro').classList.add('badge-warning');
            document.getElementById('cronometro').classList.remove('badge-danger');
        }
        
        // Alerta crítico quando falta 1 minuto
        if (this.tempoRestante === 60) {
            document.getElementById('cronometro').classList.add('badge-danger');
            document.getElementById('cronometro').classList.remove('badge-warning');
        }
    }
    
    getTempoDecorrido() {
        const tempoTotal = this.tempoLimiteMinutos * 60;
        return tempoTotal - this.tempoRestante;
    }
}
```

---

**Próximo Passo**: Implementar estes exemplos conforme o plano de desenvolvimento.
