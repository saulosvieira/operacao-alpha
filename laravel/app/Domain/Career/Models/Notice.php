<?php

namespace App\Domain\Career\Models;

use App\Domain\Career\Models\Career;
use App\Domain\Approved\Models\Approved;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'publication_date',
        'registration_start',
        'registration_end',
        'pdf_url',
        'active',
    ];

    protected $casts = [
        'publication_date' => 'date',
        'registration_start' => 'date',
        'registration_end' => 'date',
        'active' => 'bool',
    ];

    protected function examDate(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->publication_date,
            set: fn ($value) => ['publication_date' => $value],
        );
    }

    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class, 'career_id');
    }

    public function approved(): HasMany
    {
        return $this->hasMany(Approved::class, 'notice_id');
    }
}
