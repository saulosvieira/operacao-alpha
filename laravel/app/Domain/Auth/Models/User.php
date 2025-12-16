<?php

namespace App\Domain\Auth\Models;

use App\Domain\Exam\Models\UserAnswer;
use App\Domain\Exam\Models\ExamResult;
use App\Domain\Ranking\Models\Ranking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'subscription_status',
        'subscription_expires_at',
        'subscription_platform_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'subscription_expires_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    
    public function isConsultor(): bool
    {
        return $this->role === 'consultor';
    }

    public function isSubscribed(): bool
    {
        return $this->subscription_status === 'active' 
            && $this->subscription_expires_at 
            && $this->subscription_expires_at->isFuture();
    }

    public function respostas()
    {
        return $this->hasMany(UserAnswer::class);
    }

    public function resultados()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function ranking()
    {
        return $this->hasOne(Ranking::class)->latest('data_calculo');
    }
}
