<?php

namespace App\Domain\Career\Models;

use App\Domain\Career\Models\Notice;
use App\Domain\Exam\Models\Exam;
use App\Domain\Approved\Models\Approved;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Career extends Model
{
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
