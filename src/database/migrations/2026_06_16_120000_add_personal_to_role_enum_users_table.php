<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'parent', 'child', 'personal') NOT NULL DEFAULT 'child'");
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'personal')->update(['role' => 'child']);
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'parent', 'child') NOT NULL DEFAULT 'child'");
    }
};
