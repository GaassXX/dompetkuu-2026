<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        // ===== Kategori Pemasukan =====
        $incomeCategories = [
            'Gaji', 'Uang Saku', 'Hadiah',
            'Bonus', 'Investasi', 'Penjualan', 'Lainnya',
        ];

        foreach ($incomeCategories as $name) {
            Category::firstOrCreate(
                ['name' => $name, 'type' => 'income', 'is_global' => true],
                ['created_by' => $admin->id]
            );
        }

        // ===== Kategori Pengeluaran =====
        $expenseCategories = [
            'Makan & Minum', 'Transportasi', 'Jajan',
            'Top Up Game', 'Belanja', 'Pendidikan',
            'Kesehatan', 'Hiburan', 'Tagihan', 'Lainnya',
        ];

        foreach ($expenseCategories as $name) {
            Category::firstOrCreate(
                ['name' => $name, 'type' => 'expense', 'is_global' => true],
                ['created_by' => $admin->id]
            );
        }
    }
}
