<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aprovado extends Model
{
    protected $fillable = [
        'carreira_id',
        'edital_id',
        'nome',
        'posicao',
        'ano',
    ];

    protected $casts = [
        'posicao' => 'integer',
        'ano' => 'integer',
    ];

    public function carreira(): BelongsTo
    {
        return $this->belongsTo(Carreira::class);
    }

    public function edital(): BelongsTo
    {
        return $this->belongsTo(Edital::class);
    }
}
