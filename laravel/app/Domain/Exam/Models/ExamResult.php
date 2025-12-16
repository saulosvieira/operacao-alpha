<?php

namespace App\Domain\Exam\Models;

use App\Domain\Auth\Models\User;
use App\Domain\Exam\Models\Exam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamResult extends Model
{
    protected $table = 'exam_results';

    protected $fillable = [
        'user_id',
        'attempt_id',
        'exam_id',
        'score',
        'total_questions',
        'correct_answers',
        'total_time_seconds',
        'finished_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'total_questions' => 'integer',
        'correct_answers' => 'integer',
        'total_time_seconds' => 'integer',
        'finished_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(Attempt::class, 'attempt_id');
    }
}
