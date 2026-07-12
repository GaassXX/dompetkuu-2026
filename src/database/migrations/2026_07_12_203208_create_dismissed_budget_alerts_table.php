<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dismissed_budget_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('budget_id')->constrained()->onDelete('cascade');
            $table->string('period');
            $table->timestamps();

            $table->unique(['user_id', 'budget_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dismissed_budget_alerts');
    }
};
