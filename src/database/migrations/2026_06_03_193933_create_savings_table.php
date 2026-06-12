<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');                          // Nama tabungan
            $table->string('category')->nullable();          // Kategori (Liburan, Properti, dll)
            $table->decimal('target_amount', 15, 2);         // Target jumlah
            $table->decimal('current_amount', 15, 2)->default(0); // Terkumpul
            $table->date('target_date')->nullable();         // Target waktu
            $table->text('description')->nullable();         // Deskripsi
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();
        });

        Schema::create('saving_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saving_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saving_deposits');
        Schema::dropIfExists('savings');
    }
};
