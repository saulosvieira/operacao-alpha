<?php

namespace App\Domain\Complaint\Models;

use App\Domain\Auth\Models\User;
use App\Domain\Complaint\Enums\ComplaintPriority;
use App\Domain\Complaint\Enums\ComplaintStatus;
use App\Domain\Complaint\Enums\ComplaintType;
use App\Domain\Exam\Models\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    protected $fillable = [
        'question_id',
        'admin_id',
        'type',
        'description',
        'priority',
        'status',
        'resolution_note',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => ComplaintType::class,
            'priority' => ComplaintPriority::class,
            'status' => ComplaintStatus::class,
            'resolved_at' => 'datetime',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function isPending(): bool
    {
        return in_array($this->status, [ComplaintStatus::OPEN, ComplaintStatus::IN_REVIEW]);
    }
}
