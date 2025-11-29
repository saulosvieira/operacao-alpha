<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Edital extends Model
{
    protected $table = 'editais';

    protected $fillable = [
        'carreira_id',
        'titulo',
        'descricao',
        'data_publicacao',
        'ativo',
    ];

    protected $casts = [
        'data_publicacao' => 'date',
        'ativo' => 'boolean',
    ];

    public function carreira(): BelongsTo
    {
        return $this->belongsTo(Carreira::class);
    }

    public function aprovados(): HasMany
    {
        return $this->hasMany(Aprovado::class);
    }
}
