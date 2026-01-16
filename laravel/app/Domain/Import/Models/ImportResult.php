<?php

namespace App\Domain\Import\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportResult extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'session_id',
        'total_processed',
        'successful_imports',
        'failed_imports',
        'errors',
        'success_details',
        'processing_time',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'errors' => 'array',
        'success_details' => 'array',
        'total_processed' => 'integer',
        'successful_imports' => 'integer',
        'failed_imports' => 'integer',
        'processing_time' => 'integer',
    ];

    /**
     * Get the import session that this result belongs to.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(ImportSession::class, 'session_id');
    }

    /**
     * Get the success rate as a percentage.
     */
    public function getSuccessRateAttribute(): float
    {
        if ($this->total_processed === 0) {
            return 0.0;
        }

        return round(($this->successful_imports / $this->total_processed) * 100, 2);
    }

    /**
     * Get the failure rate as a percentage.
     */
    public function getFailureRateAttribute(): float
    {
        if ($this->total_processed === 0) {
            return 0.0;
        }

        return round(($this->failed_imports / $this->total_processed) * 100, 2);
    }

    /**
     * Check if the import was completely successful.
     */
    public function isCompletelySuccessful(): bool
    {
        return $this->failed_imports === 0 && $this->successful_imports > 0;
    }

    /**
     * Check if the import had any failures.
     */
    public function hasFailures(): bool
    {
        return $this->failed_imports > 0;
    }

    /**
     * Check if the import had any successes.
     */
    public function hasSuccesses(): bool
    {
        return $this->successful_imports > 0;
    }

    /**
     * Get formatted processing time.
     */
    public function getFormattedProcessingTimeAttribute(): string
    {
        if (!$this->processing_time) {
            return 'N/A';
        }

        $minutes = floor($this->processing_time / 60);
        $seconds = $this->processing_time % 60;

        if ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $seconds);
        }

        return sprintf('%ds', $seconds);
    }

    /**
     * Get errors grouped by type.
     */
    public function getGroupedErrors(): array
    {
        if (!$this->errors) {
            return [];
        }

        $grouped = [];
        foreach ($this->errors as $error) {
            $type = $error['type'] ?? 'general';
            if (!isset($grouped[$type])) {
                $grouped[$type] = [];
            }
            $grouped[$type][] = $error;
        }

        return $grouped;
    }

    /**
     * Get success details grouped by exam.
     */
    public function getSuccessDetailsByExam(): array
    {
        if (!$this->success_details) {
            return [];
        }

        $grouped = [];
        foreach ($this->success_details as $detail) {
            $examId = $detail['exam_id'] ?? 'unknown';
            if (!isset($grouped[$examId])) {
                $grouped[$examId] = [
                    'exam_name' => $detail['exam_name'] ?? 'Unknown Exam',
                    'questions' => [],
                    'count' => 0
                ];
            }
            $grouped[$examId]['questions'][] = $detail;
            $grouped[$examId]['count']++;
        }

        return $grouped;
    }

    /**
     * Get validation rules for the model.
     */
    public static function validationRules(): array
    {
        return [
            'session_id' => 'required|exists:import_sessions,id',
            'total_processed' => 'integer|min:0',
            'successful_imports' => 'integer|min:0',
            'failed_imports' => 'integer|min:0',
            'errors' => 'nullable|array',
            'success_details' => 'nullable|array',
            'processing_time' => 'nullable|integer|min:0',
        ];
    }
}