<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ranking extends Model
{
    protected $fillable = [
        'user_id',
        'pontuacao_diaria',
        'pontuacao_semanal',
        'data_calculo',
    ];

    protected $casts = [
        'pontuacao_diaria' => 'decimal:2',
        'pontuacao_semanal' => 'decimal:2',
        'data_calculo' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
