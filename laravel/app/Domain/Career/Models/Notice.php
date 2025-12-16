<?php

namespace App\Domain\Career\Models;

use App\Domain\Career\Models\Career;
use App\Domain\Approved\Models\Approved;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notice extends Model
{
    protected $table = 'notices';

    protected $fillable = [
        'career_id',
        'title',
        'description',
        'exam_date',
    ];

    protected $casts = [
        'exam_date' => 'date',
    ];

    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class, 'career_id');
    }

    public function approved(): HasMany
    {
        return $this->hasMany(Approved::class, 'notice_id');
    }
}
