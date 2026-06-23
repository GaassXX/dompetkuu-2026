<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;

class TransactionParserService
{
    /**
     * Kamus sinonim: nama_kategori => [kata kunci tambahan]
     */
    protected array $synonyms = [
        'Transportasi' => ['bensin', 'motor', 'mobil', 'ojek', 'grab', 'gojek', 'parkir', 'tol', 'angkot', 'bus', 'kereta'],
        'Makan & Minum' => ['makan', 'minum', 'kopi', 'jajan makanan', 'restoran', 'warteg', 'nasi', 'bakso', 'mie'],
        'Belanja' => ['baju', 'sepatu', 'belanja', 'shopee', 'tokopedia', 'mall'],
        'Tagihan' => ['listrik', 'pln', 'air', 'pdam', 'internet', 'wifi', 'pulsa', 'token'],
        'Kesehatan' => ['obat', 'dokter', 'apotek', 'rumah sakit', 'vitamin'],
        'Hiburan' => ['nonton', 'bioskop', 'netflix', 'spotify', 'game online', 'konser'],
        'Pendidikan' => ['buku', 'kursus', 'sekolah', 'kuliah', 'spp'],
        'Gaji' => ['gajian', 'gaji'],
        'Top Up Game' => ['top up', 'topup', 'diamond', 'voucher game', 'mobile legend', 'valorant', 'free fire'],
    ];

    public function parse(string $text, int $userId): ?array
    {
        $text = trim($text);

        if (! preg_match('/(\d+[\.,]?\d*)\s*(rb|ribu|jt|juta|k)?/i', $text, $m)) {
            return null;
        }

        $amount = (float) str_replace(',', '.', $m[1]);
        $unit = strtolower($m[2] ?? '');

        $amount = match (true) {
            in_array($unit, ['rb', 'ribu', 'k']) => $amount * 1000,
            in_array($unit, ['jt', 'juta']) => $amount * 1000000,
            default => $amount,
        };

        if ($amount <= 0) {
            return null;
        }

        $incomeKeywords = ['gaji', 'bonus', 'freelance', 'transfer masuk', 'pendapatan', 'untung', 'gajian'];
        $type = 'expense';
        foreach ($incomeKeywords as $keyword) {
            if (str_contains(strtolower($text), $keyword)) {
                $type = 'income';
                break;
            }
        }

        $category = $this->detectCategory($text, $type, $userId);
        $description = trim(preg_replace('/(\d+[\.,]?\d*)\s*(rb|ribu|jt|juta|k)?/i', '', $text));

        return [
            'type' => $type,
            'amount' => $amount,
            'description' => $description ?: 'Transaksi',
            'category_id' => $category?->id,
        ];
    }

    public function detectCategory(string $text, string $type, int $userId): ?Category
    {
        $text = strtolower($text);

        $categories = Category::query()
            ->where('type', $type)
            ->where(function ($q) use ($userId) {
                $q->where('is_global', true)
                  ->orWhere('created_by', $userId);
            })
            ->get();

        // 1. Coba match lewat kamus sinonim dulu (lebih akurat)
        foreach ($categories as $category) {
            $synonymList = $this->synonyms[$category->name] ?? [];
            foreach ($synonymList as $synonym) {
                if (str_contains($text, strtolower($synonym))) {
                    return $category;
                }
            }
        }

        // 2. Fallback: match dari nama kategori itu sendiri (pecah per kata)
        return $categories->first(function ($category) use ($text) {
            $keywords = explode(' ', strtolower(str_replace('&', ' ', $category->name)));
            foreach ($keywords as $keyword) {
                if (strlen($keyword) >= 3 && str_contains($text, $keyword)) {
                    return true;
                }
            }
            return false;
        });
    }

    public function save(array $parsed, int $userId): Expense|Income
    {
        $model = $parsed['type'] === 'income' ? Income::class : Expense::class;

        return $model::create([
            'user_id' => $userId,
            'category_id' => $parsed['category_id'],
            'amount' => $parsed['amount'],
            'description' => $parsed['description'],
            'date' => now(),
            'status' => 'approved',
        ]);
    }
}
