<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carreira extends Model
{
    protected $fillable = [
        'nome',
        'descricao',
        'slug',
        'ativa',
    ];

    protected $casts = [
        'ativa' => 'boolean',
    ];

    public function editais(): HasMany
    {
        return $this->hasMany(Edital::class);
    }

    public function simulados(): HasMany
    {
        return $this->hasMany(Simulado::class);
    }

    public function aprovados(): HasMany
    {
        return $this->hasMany(Aprovado::class);
    }
}
