<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Career\Actions\Admin\ListNoticesForAdminAction;
use App\Domain\Career\Actions\Admin\GetNoticeForEditAction;
use App\Domain\Career\Actions\Admin\ListActiveCareersAction;
use App\Domain\Career\Actions\Admin\CreateNoticeAction;
use App\Domain\Career\Actions\Admin\UpdateNoticeAction;
use App\Domain\Career\Actions\Admin\DeleteNoticeAction;
use App\Domain\Career\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function index(ListNoticesForAdminAction $action)
    {
        $notices = $action->execute();
            
        return view('admin.notices.index', compact('notices'));
    }
    
    public function create(ListActiveCareersAction $action)
    {
        $careers = $action->execute();
            
        return view('admin.notices.create', compact('careers'));
    }
    
    public function store(Request $request, CreateNoticeAction $action)
    {
        $validated = $request->validate([
            'career_id' => 'required|exists:careers,id',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'exam_date' => 'nullable|date',
            'active' => 'boolean',
        ]);
        
        $action->execute(
            careerId: $validated['career_id'],
            title: $validated['title'],
            description: $validated['description'] ?? null,
            examDate: $validated['exam_date'] ?? null,
            active: $request->has('active')
        );
        
        return redirect()
            ->route('admin.notices.index')
            ->with('success', 'Notice created successfully!');
    }
    
    public function edit(Notice $notice, GetNoticeForEditAction $getNoticeAction, ListActiveCareersAction $listCareersAction)
    {
        $noticeData = $getNoticeAction->execute($notice->id);
        $careers = $listCareersAction->execute();
            
        return view('admin.notices.edit', ['notice' => $noticeData, 'careers' => $careers]);
    }
    
    public function update(Request $request, Notice $notice, UpdateNoticeAction $action)
    {
        $validated = $request->validate([
            'career_id' => 'required|exists:careers,id',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'exam_date' => 'nullable|date',
            'active' => 'boolean',
        ]);
        
        $action->execute(
            noticeId: $notice->id,
            careerId: $validated['career_id'],
            title: $validated['title'],
            description: $validated['description'] ?? null,
            examDate: $validated['exam_date'] ?? null,
            active: $request->has('active')
        );
        
        return redirect()
            ->route('admin.notices.index')
            ->with('success', 'Notice updated successfully!');
    }
    
    public function destroy(Notice $notice, DeleteNoticeAction $action)
    {
        $action->execute($notice->id);
        
        return redirect()
            ->route('admin.notices.index')
            ->with('success', 'Notice deleted successfully!');
    }
}
