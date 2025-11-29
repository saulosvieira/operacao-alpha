<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Questao extends Model
{
    protected $table = 'questoes';

    protected $fillable = [
        'simulado_id',
        'numero_questao',
        'enunciado',
        'imagem_enunciado',
        'alternativa_a',
        'imagem_a',
        'alternativa_b',
        'imagem_b',
        'alternativa_c',
        'imagem_c',
        'alternativa_d',
        'imagem_d',
        'alternativa_e',
        'imagem_e',
        'resposta_correta',
        'explicacao',
    ];

    protected $casts = [
        'numero_questao' => 'integer',
    ];

    public function simulado(): BelongsTo
    {
        return $this->belongsTo(Simulado::class);
    }

    public function respostas(): HasMany
    {
        return $this->hasMany(RespostaUsuario::class);
    }
}
