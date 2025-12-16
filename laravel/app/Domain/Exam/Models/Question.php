<?php

namespace App\Domain\Exam\Models;

use App\Domain\Exam\Models\Exam;
use App\Domain\Exam\Models\UserAnswer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $table = 'questions';

    protected $fillable = [
        'exam_id',
        'question_number',
        'statement',
        'statement_image',
        'option_a',
        'option_a_image',
        'option_b',
        'option_b_image',
        'option_c',
        'option_c_image',
        'option_d',
        'option_d_image',
        'option_e',
        'option_e_image',
        'correct_answer',
        'explanation',
    ];

    protected $casts = [
        'question_number' => 'integer',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(UserAnswer::class, 'question_id');
    }
}
