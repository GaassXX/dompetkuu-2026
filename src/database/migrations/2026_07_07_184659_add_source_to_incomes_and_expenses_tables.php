<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->string('source')->nullable()->after('amount');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->string('source')->nullable()->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropColumn('source');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
