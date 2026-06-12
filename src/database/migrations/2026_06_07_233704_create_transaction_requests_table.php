<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parent_id')->constrained('users')->cascadeOnDelete();
            $table->string('type'); // 'delete' atau 'edit'
            $table->string('model_type'); // 'income' atau 'expense'
            $table->unsignedBigInteger('model_id');
            $table->json('old_data')->nullable(); // data lama
            $table->json('new_data')->nullable(); // data baru (untuk edit)
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reason')->nullable(); // alasan request
            $table->text('reject_reason')->nullable(); // alasan ditolak
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_requests');
    }
};
