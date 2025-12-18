<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuestionRequest;
use App\Http\Requests\Admin\UpdateQuestionRequest;
use App\Domain\Exam\Actions\Admin\CreateQuestionAction;
use App\Domain\Exam\Actions\Admin\UpdateQuestionAction;
use App\Domain\Exam\Actions\Admin\DeleteQuestionAction;
use App\Domain\Exam\Actions\Admin\ListQuestionsForExamAction;
use App\Domain\Exam\DTOs\Admin\QuestionFormData;
use App\Domain\Exam\Models\Exam;
use App\Domain\Exam\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QuestionController extends Controller
{
    /**
     * Display a listing of questions for an exam.
     */
    public function index(Exam $exam, ListQuestionsForExamAction $action): View
    {
        $questions = $action->execute($exam->id);

        return view('admin.questions.index', compact('exam', 'questions'));
    }

    /**
     * Show the form for creating a new question.
     */
    public function create(Exam $exam): View
    {
        return view('admin.questions.create', compact('exam'));
    }

    /**
     * Store a newly created question in storage.
     */
    public function store(
        StoreQuestionRequest $request,
        Exam $exam,
        CreateQuestionAction $action
    ): RedirectResponse {
        $data = QuestionFormData::fromRequest($request);
        $action->execute($exam->id, $data);

        return redirect()
            ->route('admin.exams.questions.index', $exam)
            ->with('success', 'Questão criada com sucesso!');
    }

    /**
     * Show the form for editing the specified question.
     */
    public function edit(Exam $exam, Question $question): View
    {
        return view('admin.questions.edit', compact('exam', 'question'));
    }

    /**
     * Update the specified question in storage.
     */
    public function update(
        UpdateQuestionRequest $request,
        Exam $exam,
        Question $question,
        UpdateQuestionAction $action
    ): RedirectResponse {
        $data = QuestionFormData::fromRequest($request);
        $action->execute($question->id, $data);

        return redirect()
            ->route('admin.exams.questions.index', $exam)
            ->with('success', 'Questão atualizada com sucesso!');
    }

    /**
     * Remove the specified question from storage.
     */
    public function destroy(
        Exam $exam,
        Question $question,
        DeleteQuestionAction $action
    ): RedirectResponse {
        $action->execute($question->id);

        return redirect()
            ->route('admin.exams.questions.index', $exam)
            ->with('success', 'Questão excluída com sucesso!');
    }
}
