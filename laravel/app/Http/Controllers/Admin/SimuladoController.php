<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Simulado;
use App\Models\Carreira;
use Illuminate\Http\Request;

class SimuladoController extends Controller
{
    public function index()
    {
        $simulados = Simulado::with('carreira')
            ->withCount('questoes')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.simulados.index', compact('simulados'));
    }
    
    public function create()
    {
        $carreiras = Carreira::where('ativa', true)
            ->orderBy('nome')
            ->get();
            
        return view('admin.simulados.create', compact('carreiras'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'carreira_id' => 'required|exists:carreiras,id',
            'titulo' => 'required|string|max:200',
            'descricao' => 'nullable|string',
            'tempo_limite_minutos' => 'required|integer|min:1|max:300',
            'ativo' => 'boolean',
        ]);
        
        $validated['ativo'] = $request->has('ativo');
        
        Simulado::create($validated);
        
        return redirect()
            ->route('admin.simulados.index')
            ->with('success', 'Simulado criado com sucesso!');
    }
    
    public function edit(Simulado $simulado)
    {
        $carreiras = Carreira::where('ativa', true)
            ->orderBy('nome')
            ->get();
            
        $simulado->loadCount('questoes');
            
        return view('admin.simulados.edit', compact('simulado', 'carreiras'));
    }
    
    public function update(Request $request, Simulado $simulado)
    {
        $validated = $request->validate([
            'carreira_id' => 'required|exists:carreiras,id',
            'titulo' => 'required|string|max:200',
            'descricao' => 'nullable|string',
            'tempo_limite_minutos' => 'required|integer|min:1|max:300',
            'ativo' => 'boolean',
        ]);
        
        $validated['ativo'] = $request->has('ativo');
        
        $simulado->update($validated);
        
        return redirect()
            ->route('admin.simulados.index')
            ->with('success', 'Simulado atualizado com sucesso!');
    }
    
    public function destroy(Simulado $simulado)
    {
        if ($simulado->questoes()->count() > 0) {
            return back()->with('error', 'Não é possível excluir simulado com questões associadas.');
        }
        
        $simulado->delete();
        
        return redirect()
            ->route('admin.simulados.index')
            ->with('success', 'Simulado excluído com sucesso!');
    }
}
