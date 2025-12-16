<?php

namespace App\Domain\Exam\Models;

use App\Domain\Career\Models\Career;
use App\Domain\Exam\Models\Question;
use App\Domain\Exam\Models\ExamResult;
use App\Domain\Exam\Models\UserAnswer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    protected $table = 'exams';

    protected $fillable = [
        'career_id',
        'title',
        'description',
        'time_limit_minutes',
        'active',
        'is_free',
    ];

    protected $casts = [
        'time_limit_minutes' => 'integer',
        'active' => 'boolean',
        'is_free' => 'boolean',
    ];

    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class, 'career_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'exam_id')->orderBy('question_number');
    }

    public function results(): HasMany
    {
        return $this->hasMany(ExamResult::class, 'exam_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class, 'exam_id');
    }
}
