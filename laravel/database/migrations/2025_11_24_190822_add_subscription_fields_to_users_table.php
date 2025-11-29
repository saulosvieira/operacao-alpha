<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('subscription_status', ['inactive', 'active', 'trial', 'expired'])
                  ->default('inactive')
                  ->after('password');
            $table->timestamp('subscription_expires_at')->nullable()->after('subscription_status');
            $table->string('subscription_platform_id', 100)->nullable()->after('subscription_expires_at');
            
            $table->index('subscription_status');
            $table->index('subscription_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['subscription_status']);
            $table->dropIndex(['subscription_expires_at']);
            $table->dropColumn(['subscription_status', 'subscription_expires_at', 'subscription_platform_id']);
        });
    }
};
