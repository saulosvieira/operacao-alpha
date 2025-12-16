<?php

namespace App\Domain\Approved\Models;

use App\Domain\Career\Models\Career;
use App\Domain\Career\Models\Notice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Approved extends Model
{
    protected $table = 'approved';

    protected $fillable = [
        'career_id',
        'notice_id',
        'name',
        'position',
        'year',
    ];

    protected $casts = [
        'position' => 'integer',
        'year' => 'integer',
    ];

    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class, 'career_id');
    }

    public function notice(): BelongsTo
    {
        return $this->belongsTo(Notice::class, 'notice_id');
    }
}
