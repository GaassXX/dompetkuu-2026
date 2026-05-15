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
        $table->enum('role', ['admin', 'parent', 'child'])->default('child')->after('email');
        $table->foreignId('parent_id')->nullable()->constrained('users')->nullOnDelete()->after('role');
        $table->boolean('is_active')->default(true)->after('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['parent_id']);
        $table->dropColumn(['role', 'parent_id', 'is_active']);
        });
    }
};
