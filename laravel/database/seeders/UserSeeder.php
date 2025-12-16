<?php

namespace Database\Seeders;

use App\Domain\Auth\Models\User;
use App\Domain\Auth\Enums\UserRole;
use App\Domain\Auth\Enums\SubscriptionStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Consultant with active subscription
        User::create([
            'name' => 'Carlos Consultor',
            'email' => 'consultor@test.com',
            'phone' => '11987654321',
            'password' => Hash::make('password123'),
            'role' => UserRole::CONSULTANT->value,
            'subscription_status' => SubscriptionStatus::ACTIVE->value,
            'subscription_expires_at' => now()->addYear(),
            'email_verified_at' => now(),
        ]);
        
        // Premium user with active subscription
        User::create([
            'name' => 'Maria Premium',
            'email' => 'premium@test.com',
            'phone' => '11987654322',
            'password' => Hash::make('password123'),
            'role' => UserRole::USER->value,
            'subscription_status' => SubscriptionStatus::ACTIVE->value,
            'subscription_expires_at' => now()->addMonths(6),
            'email_verified_at' => now(),
        ]);
        
        // User on trial
        User::create([
            'name' => 'João Trial',
            'email' => 'trial@test.com',
            'phone' => '11987654323',
            'password' => Hash::make('password123'),
            'role' => UserRole::USER->value,
            'subscription_status' => SubscriptionStatus::TRIAL->value,
            'subscription_expires_at' => now()->addDays(7),
            'email_verified_at' => now(),
        ]);
        
        // Free user (inactive subscription)
        User::create([
            'name' => 'Ana Free',
            'email' => 'free@test.com',
            'phone' => '11987654324',
            'password' => Hash::make('password123'),
            'role' => UserRole::USER->value,
            'subscription_status' => SubscriptionStatus::INACTIVE->value,
            'subscription_expires_at' => null,
            'email_verified_at' => now(),
        ]);
        
        // User with expired subscription
        User::create([
            'name' => 'Pedro Expirado',
            'email' => 'expired@test.com',
            'phone' => '11987654325',
            'password' => Hash::make('password123'),
            'role' => UserRole::USER->value,
            'subscription_status' => SubscriptionStatus::EXPIRED->value,
            'subscription_expires_at' => now()->subDays(10),
            'email_verified_at' => now(),
        ]);
        
        // Additional test users for ranking/attempts
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => "Usuário Teste {$i}",
                'email' => "user{$i}@test.com",
                'phone' => "1198765432{$i}",
                'password' => Hash::make('password123'),
                'role' => UserRole::USER->value,
                'subscription_status' => $i % 2 === 0 ? SubscriptionStatus::ACTIVE->value : SubscriptionStatus::INACTIVE->value,
                'subscription_expires_at' => $i % 2 === 0 ? now()->addMonths(3) : null,
                'email_verified_at' => now(),
            ]);
        }
    }
}
