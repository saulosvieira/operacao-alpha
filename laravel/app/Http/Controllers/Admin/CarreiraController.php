<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carreira;
use Illuminate\Http\Request;
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
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:100',
            'descricao' => 'nullable|string',
            'ativa' => 'boolean',
        ]);
        
        $validated['slug'] = Str::slug($validated['nome']);
        $validated['ativa'] = $request->has('ativa');
        
        Carreira::create($validated);
        
        return redirect()
            ->route('admin.carreiras.index')
            ->with('success', 'Carreira criada com sucesso!');
    }
    
    public function edit(Carreira $carreira)
    {
        return view('admin.carreiras.edit', compact('carreira'));
    }
    
    public function update(Request $request, Carreira $carreira)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:100',
            'descricao' => 'nullable|string',
            'ativa' => 'boolean',
        ]);
        
        $validated['slug'] = Str::slug($validated['nome']);
        $validated['ativa'] = $request->has('ativa');
        
        $carreira->update($validated);
        
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
