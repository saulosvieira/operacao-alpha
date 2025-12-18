<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Exam\Actions\Admin\ListExamsForAdminAction;
use App\Domain\Exam\Actions\Admin\GetExamForEditAction;
use App\Domain\Exam\Actions\Admin\CreateExamAction;
use App\Domain\Exam\Actions\Admin\UpdateExamAction;
use App\Domain\Exam\Actions\Admin\DeleteExamAction;
use App\Domain\Career\Actions\Admin\ListActiveCareersAction;
use App\Domain\Exam\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(ListExamsForAdminAction $action)
    {
        $exams = $action->execute();
            
        return view('admin.exams.index', compact('exams'));
    }
    
    public function create(ListActiveCareersAction $action)
    {
        $careers = $action->execute();
            
        return view('admin.exams.create', compact('careers'));
    }
    
    public function store(Request $request, CreateExamAction $action)
    {
        $validated = $request->validate([
            'career_id' => 'required|exists:careers,id',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'time_limit_minutes' => 'required|integer|min:1|max:300',
            'feedback_mode' => 'required|in:immediate,final',
            'active' => 'nullable|boolean',
            'is_free' => 'nullable|boolean',
        ]);
        
        $action->execute(
            careerId: $validated['career_id'],
            title: $validated['title'],
            description: $validated['description'] ?? null,
            timeLimitMinutes: $validated['time_limit_minutes'],
            feedbackMode: $validated['feedback_mode'],
            active: (bool) ($validated['active'] ?? false),
            isFree: (bool) ($validated['is_free'] ?? false)
        );
        
        return redirect()
            ->route('admin.exams.index')
            ->with('success', 'Simulado criado com sucesso!');
    }
    
    public function edit(Exam $exam, GetExamForEditAction $getExamAction, ListActiveCareersAction $listCareersAction)
    {
        $examData = $getExamAction->execute($exam->id);
        $careers = $listCareersAction->execute();
            
        return view('admin.exams.edit', ['exam' => $examData, 'careers' => $careers]);
    }
    
    public function update(Request $request, Exam $exam, UpdateExamAction $action)
    {
        $validated = $request->validate([
            'career_id' => 'required|exists:careers,id',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'time_limit_minutes' => 'required|integer|min:1|max:300',
            'feedback_mode' => 'required|in:immediate,final',
            'active' => 'nullable|boolean',
            'is_free' => 'nullable|boolean',
        ]);
        
        $action->execute(
            examId: $exam->id,
            careerId: $validated['career_id'],
            title: $validated['title'],
            description: $validated['description'] ?? null,
            timeLimitMinutes: $validated['time_limit_minutes'],
            feedbackMode: $validated['feedback_mode'],
            active: (bool) ($validated['active'] ?? false),
            isFree: (bool) ($validated['is_free'] ?? false)
        );
        
        return redirect()
            ->route('admin.exams.index')
            ->with('success', 'Simulado atualizado com sucesso!');
    }
    
    public function destroy(Exam $exam, DeleteExamAction $action)
    {
        try {
            $action->execute($exam->id);
            
            return redirect()
                ->route('admin.exams.index')
                ->with('success', 'Simulado excluído com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Não é possível excluir simulado com questões associadas.');
        }
    }
}
