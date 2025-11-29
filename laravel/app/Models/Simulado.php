<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Simulado extends Model
{
    protected $fillable = [
        'carreira_id',
        'titulo',
        'descricao',
        'tempo_limite_minutos',
        'ativo',
    ];

    protected $casts = [
        'tempo_limite_minutos' => 'integer',
        'ativo' => 'boolean',
    ];

    public function carreira(): BelongsTo
    {
        return $this->belongsTo(Carreira::class);
    }

    public function questoes(): HasMany
    {
        return $this->hasMany(Questao::class)->orderBy('numero_questao');
    }

    public function resultados(): HasMany
    {
        return $this->hasMany(ResultadoSimulado::class);
    }

    public function respostas(): HasMany
    {
        return $this->hasMany(RespostaUsuario::class);
    }
}
