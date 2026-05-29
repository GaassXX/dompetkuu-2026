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
        DB::statement("CREATE OR REPLACE VIEW transactions_view AS
            SELECT
                CONCAT('I-', incomes.id) as id,
                incomes.id as original_id,
                users.name as user_name,
                categories.name as category_name,
                incomes.amount,
                incomes.date,
                incomes.status,
                incomes.description,
                incomes.created_at,
                incomes.updated_at,
                COALESCE(approver.name, '') as approved_by_name,
                'Pemasukan' as type,
                incomes.user_id
            FROM incomes
            JOIN users ON incomes.user_id = users.id
            JOIN categories ON incomes.category_id = categories.id
            LEFT JOIN users as approver ON incomes.approved_by = approver.id

            UNION ALL

            SELECT
                CONCAT('E-', expenses.id) as id,
                expenses.id as original_id,
                users.name as user_name,
                categories.name as category_name,
                expenses.amount,
                expenses.date,
                expenses.status,
                expenses.description,
                expenses.created_at,
                expenses.updated_at,
                COALESCE(approver.name, '') as approved_by_name,
                'Pengeluaran' as type,
                expenses.user_id
            FROM expenses
            JOIN users ON expenses.user_id = users.id
            JOIN categories ON expenses.category_id = categories.id
            LEFT JOIN users as approver ON expenses.approved_by = approver.id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS transactions_view");
    }
};
