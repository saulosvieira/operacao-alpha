<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RespostaUsuario extends Model
{
    protected $table = 'respostas_usuarios';

    protected $fillable = [
        'user_id',
        'simulado_id',
        'questao_id',
        'resposta_escolhida',
        'correta',
        'tempo_resposta_segundos',
    ];

    protected $casts = [
        'correta' => 'boolean',
        'tempo_resposta_segundos' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function simulado(): BelongsTo
    {
        return $this->belongsTo(Simulado::class);
    }

    public function questao(): BelongsTo
    {
        return $this->belongsTo(Questao::class);
    }
}
