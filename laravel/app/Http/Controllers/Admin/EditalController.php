<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edital;
use App\Models\Carreira;
use Illuminate\Http\Request;

class EditalController extends Controller
{
    public function index()
    {
        $editais = Edital::with('carreira')
            ->orderBy('data_publicacao', 'desc')
            ->paginate(15);
            
        return view('admin.editais.index', compact('editais'));
    }
    
    public function create()
    {
        $carreiras = Carreira::where('ativa', true)
            ->orderBy('nome')
            ->get();
            
        return view('admin.editais.create', compact('carreiras'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'carreira_id' => 'required|exists:carreiras,id',
            'titulo' => 'required|string|max:200',
            'descricao' => 'nullable|string',
            'data_publicacao' => 'nullable|date',
            'ativo' => 'boolean',
        ]);
        
        $validated['ativo'] = $request->has('ativo');
        
        Edital::create($validated);
        
        return redirect()
            ->route('admin.editais.index')
            ->with('success', 'Edital criado com sucesso!');
    }
    
    public function edit(Edital $edital)
    {
        $carreiras = Carreira::where('ativa', true)
            ->orderBy('nome')
            ->get();
            
        return view('admin.editais.edit', compact('edital', 'carreiras'));
    }
    
    public function update(Request $request, Edital $edital)
    {
        $validated = $request->validate([
            'carreira_id' => 'required|exists:carreiras,id',
            'titulo' => 'required|string|max:200',
            'descricao' => 'nullable|string',
            'data_publicacao' => 'nullable|date',
            'ativo' => 'boolean',
        ]);
        
        $validated['ativo'] = $request->has('ativo');
        
        $edital->update($validated);
        
        return redirect()
            ->route('admin.editais.index')
            ->with('success', 'Edital atualizado com sucesso!');
    }
    
    public function destroy(Edital $edital)
    {
        $edital->delete();
        
        return redirect()
            ->route('admin.editais.index')
            ->with('success', 'Edital excluído com sucesso!');
    }
}
