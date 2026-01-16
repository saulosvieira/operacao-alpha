<?php

namespace App\Domain\Import\Models;

use App\Domain\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ImportSession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'filename',
        'file_path',
        'total_rows',
        'career_mappings',
        'validation_errors',
        'status',
        'created_by',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'career_mappings' => 'array',
        'validation_errors' => 'array',
        'expires_at' => 'datetime',
    ];

    /**
     * The possible status values for import sessions.
     */
    public const STATUS_UPLOADED = 'uploaded';
    public const STATUS_MAPPED = 'mapped';
    public const STATUS_PREVIEWED = 'previewed';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    /**
     * Get the user who created this import session.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the import result for this session.
     */
    public function result(): HasOne
    {
        return $this->hasOne(ImportResult::class, 'session_id');
    }

    /**
     * Check if the session has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if the session is in a specific status.
     */
    public function hasStatus(string $status): bool
    {
        return $this->status === $status;
    }

    /**
     * Check if the session is completed.
     */
    public function isCompleted(): bool
    {
        return $this->hasStatus(self::STATUS_COMPLETED);
    }

    /**
     * Check if the session has failed.
     */
    public function hasFailed(): bool
    {
        return $this->hasStatus(self::STATUS_FAILED);
    }

    /**
     * Check if the session is currently processing.
     */
    public function isProcessing(): bool
    {
        return $this->hasStatus(self::STATUS_PROCESSING);
    }

    /**
     * Get validation rules for the model.
     */
    public static function validationRules(): array
    {
        return [
            'filename' => 'required|string|max:255',
            'file_path' => 'required|string|max:500',
            'total_rows' => 'integer|min:0',
            'career_mappings' => 'nullable|array',
            'validation_errors' => 'nullable|array',
            'status' => 'required|in:uploaded,mapped,previewed,processing,completed,failed',
            'created_by' => 'required|exists:users,id',
            'expires_at' => 'nullable|date|after:now',
        ];
    }
}