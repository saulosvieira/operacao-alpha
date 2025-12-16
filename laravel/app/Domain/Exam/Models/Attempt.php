<?php

namespace App\Domain\Exam\Models;

use App\Domain\Auth\Models\User;
use App\Domain\Exam\Models\Exam;
use App\Domain\Exam\Models\UserAnswer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attempt extends Model
{
    protected $table = 'attempts';

    protected $fillable = [
        'user_id',
        'exam_id',
        'started_at',
        'finished_at',
        'duration_seconds',
        'correct_answers',
        'score',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'duration_seconds' => 'integer',
        'correct_answers' => 'integer',
        'score' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(UserAnswer::class, 'attempt_id');
    }
}
