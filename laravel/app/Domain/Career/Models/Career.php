<?php

namespace App\Domain\Career\Models;

use App\Domain\Career\Models\Notice;
use App\Domain\Exam\Models\Exam;
use App\Domain\Approved\Models\Approved;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Career extends Model
{
    /** @use HasFactory<\Database\Factories\CareerFactory> */
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\CareerFactory::new();
    }

    protected $table = 'careers';

    protected $fillable = [
        'name',
        'description',
        'slug',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function notices(): HasMany
    {
        return $this->hasMany(Notice::class, 'career_id');
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'career_id');
    }

    public function approved(): HasMany
    {
        return $this->hasMany(Approved::class, 'career_id');
    }
}
