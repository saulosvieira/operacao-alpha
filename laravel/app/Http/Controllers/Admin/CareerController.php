<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Career\Actions\Admin\ListCareersForAdminAction;
use App\Domain\Career\Actions\Admin\GetCareerForEditAction;
use App\Domain\Career\Actions\Admin\CreateCareerAction;
use App\Domain\Career\Actions\Admin\UpdateCareerAction;
use App\Domain\Career\Actions\Admin\DeleteCareerAction;
use App\Domain\Career\Models\Career;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    public function index(ListCareersForAdminAction $action)
    {
        $careers = $action->execute();
            
        return view('admin.careers.index', compact('careers'));
    }
    
    public function create()
    {
        return view('admin.careers.create');
    }
    
    public function store(Request $request, CreateCareerAction $action)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'active' => 'boolean',
        ]);
        
        $action->execute(
            name: $validated['name'],
            description: $validated['description'] ?? null,
            active: $request->has('active')
        );
        
        return redirect()
            ->route('admin.careers.index')
            ->with('success', 'Career created successfully!');
    }
    
    public function edit(Career $career, GetCareerForEditAction $action)
    {
        $careerData = $action->execute($career->id);
        return view('admin.careers.edit', ['career' => $careerData]);
    }
    
    public function update(Request $request, Career $career, UpdateCareerAction $action)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'active' => 'boolean',
        ]);
        
        $action->execute(
            careerId: $career->id,
            name: $validated['name'],
            description: $validated['description'] ?? null,
            active: $request->has('active')
        );
        
        return redirect()
            ->route('admin.careers.index')
            ->with('success', 'Career updated successfully!');
    }
    
    public function destroy(Career $career, DeleteCareerAction $action)
    {
        try {
            $action->execute($career->id);
            
            return redirect()
                ->route('admin.careers.index')
                ->with('success', 'Career deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete career with associated exams.');
        }
    }
}
