<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Modul Utang & Piutang.
     *
     * Tabel `debts` mencatat kewajiban/tagihan (bukan arus kas).
     * Arus kas baru tercatat di `incomes`/`expenses` saat pelunasan,
     * ditautkan lewat kolom `debt_id` untuk traceability.
     */
    public function up(): void
    {
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // debt = utang saya ke orang lain, receivable = piutang (orang lain utang ke saya)
            $table->enum('type', ['debt', 'receivable']);
            $table->string('person_name');
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->date('due_date')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'type']);
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->foreignId('debt_id')->nullable()->after('category_id')
                ->constrained('debts')->nullOnDelete();
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('debt_id')->nullable()->after('category_id')
                ->constrained('debts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('debt_id');
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('debt_id');
        });

        Schema::dropIfExists('debts');
    }
};
