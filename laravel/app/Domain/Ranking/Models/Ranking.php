<?php

namespace App\Domain\Ranking\Models;

use App\Domain\Auth\Models\User;
use App\Domain\Career\Models\Career;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ranking extends Model
{
    protected $fillable = [
        'user_id',
        'career_id',
        'type',
        'total_score',
        'average_correct',
        'total_exams',
        'position',
        'calculated_at',
    ];

    protected $casts = [
        'total_score' => 'decimal:2',
        'average_correct' => 'decimal:2',
        'total_exams' => 'integer',
        'position' => 'integer',
        'calculated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }
}
