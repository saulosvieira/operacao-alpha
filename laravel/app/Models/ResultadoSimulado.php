<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultadoSimulado extends Model
{
    protected $table = 'resultados_simulados';

    protected $fillable = [
        'user_id',
        'simulado_id',
        'pontuacao',
        'total_questoes',
        'acertos',
        'tempo_total_segundos',
        'finalizado_em',
    ];

    protected $casts = [
        'pontuacao' => 'decimal:2',
        'total_questoes' => 'integer',
        'acertos' => 'integer',
        'tempo_total_segundos' => 'integer',
        'finalizado_em' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function simulado(): BelongsTo
    {
        return $this->belongsTo(Simulado::class);
    }
}
