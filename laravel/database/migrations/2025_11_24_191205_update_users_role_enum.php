<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite doesn't support MODIFY COLUMN, so we'll use a different approach
        if (DB::getDriverName() === 'sqlite') {
            // For SQLite, we'll recreate the table with the new enum values
            Schema::table('users', function (Blueprint $table) {
                $table->string('role_temp')->default('user');
            });
            
            DB::statement("UPDATE users SET role_temp = role");
            
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
            
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('role_temp', 'role');
            });
        } else {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'consultor', 'user') DEFAULT 'user'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // For SQLite, reverse the process
            Schema::table('users', function (Blueprint $table) {
                $table->string('role_temp')->default('consultor');
            });
            
            DB::statement("UPDATE users SET role_temp = role WHERE role IN ('admin', 'consultor')");
            
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
            
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('role_temp', 'role');
            });
        } else {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'consultor') DEFAULT 'consultor'");
        }
    }
};
