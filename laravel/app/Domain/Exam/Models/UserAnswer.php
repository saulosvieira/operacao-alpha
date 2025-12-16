<?php

namespace App\Domain\Exam\Models;

use App\Domain\Auth\Models\User;
use App\Domain\Exam\Models\Exam;
use App\Domain\Exam\Models\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAnswer extends Model
{
    protected $table = 'user_answers';

    protected $fillable = [
        'user_id',
        'attempt_id',
        'question_id',
        'chosen_answer',
        'correct',
        'time_seconds',
    ];

    protected $casts = [
        'correct' => 'boolean',
        'time_seconds' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(Attempt::class, 'attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
