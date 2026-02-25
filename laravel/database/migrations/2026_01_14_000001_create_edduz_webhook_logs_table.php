<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('edduz_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id', 255)->unique()->nullable();
            $table->string('event_type', 50)->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->index();
            $table->json('payload');
            $table->string('ip_address', 45);
            $table->json('headers');
            $table->string('processing_status', 20)->index();
            $table->text('error_message')->nullable();
            $table->timestamp('received_at')->index();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edduz_webhook_logs');
    }
};
