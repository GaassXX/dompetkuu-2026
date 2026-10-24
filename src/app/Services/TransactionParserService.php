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
        'Makan & Minum' => ['makan', 'minum', 'kopi', 'jajan makanan', 'restoran', 'warteg', 'nasi', 'bakso', 'mie', 'sarapan', 'siang', 'malam'],
        'Belanja' => ['baju', 'sepatu', 'belanja', 'shopee', 'tokopedia', 'mall', 'supermarket', 'pasar'],
        'Tagihan' => ['listrik', 'pln', 'air', 'pdam', 'internet', 'wifi', 'pulsa', 'token', 'iuran'],
        'Kesehatan' => ['obat', 'dokter', 'apotek', 'rumah sakit', 'vitamin'],
        'Hiburan' => ['nonton', 'bioskop', 'netflix', 'spotify', 'game online', 'konser'],
        'Pendidikan' => ['buku', 'kursus', 'sekolah', 'kuliah', 'spp'],
        'Gaji' => ['gajian', 'gaji', 'salary'],
        'Top Up Game' => ['top up', 'topup', 'diamond', 'voucher game', 'mobile legend', 'valorant', 'free fire'],
    ];

    public function parse(string $text, int $userId): ?array
    {
        $text = trim($text);

        // Regex untuk menangkap angka dengan format ribuan Indo (10.000 / 1.000.000) atau desimal (1.5 / 1,5)
        // Match: (Rp/rp)? diikuti angka dengan titik/koma dan unit (k, rb, jt, dll)
        $pattern = '/(?:rp\.?\s*)?(\d{1,3}(?:\.\d{3})+(?:,\d+)?|\d+(?:[.,]\d+)?)\s*(rb|ribu|jt|juta|k)?/i';

        if (! preg_match($pattern, $text, $m)) {
            return null;
        }

        $rawNumber = $m[1];
        $unit = strtolower($m[2] ?? '');

        // Jika angka mengandung format ribuan seperti 10.000 atau 1.000.000
        if (str_contains($rawNumber, '.') && !str_contains($rawNumber, ',')) {
            $parts = explode('.', $rawNumber);
            // Cek apakah segmen setelah titik selalu 3 digit (format ribuan Indo)
            $isThousandFormat = true;
            for ($i = 1; $i < count($parts); $i++) {
                if (strlen($parts[$i]) !== 3) {
                    $isThousandFormat = false;
                    break;
                }
            }
            if ($isThousandFormat && count($parts) > 1) {
                $amount = (float) str_replace('.', '', $rawNumber);
            } else {
                $amount = (float) $rawNumber;
            }
        } elseif (str_contains($rawNumber, ',') && str_contains($rawNumber, '.')) {
            // Contoh 1.500.000,50
            $amount = (float) str_replace(',', '.', str_replace('.', '', $rawNumber));
        } else {
            // Contoh 15,5 atau 15000
            $amount = (float) str_replace(',', '.', $rawNumber);
        }

        $amount = match (true) {
            in_array($unit, ['rb', 'ribu', 'k']) => $amount * 1000,
            in_array($unit, ['jt', 'juta']) => $amount * 1000000,
            default => $amount,
        };

        if ($amount <= 0) {
            return null;
        }

        $incomeKeywords = ['gaji', 'bonus', 'freelance', 'transfer masuk', 'pendapatan', 'untung', 'gajian', 'piutang'];
        $type = 'expense';
        foreach ($incomeKeywords as $keyword) {
            if (str_contains(strtolower($text), $keyword)) {
                $type = 'income';
                break;
            }
        }

        $category = $this->detectCategory($text, $type, $userId);

        // Hapus bagian angka dari teks agar mendapatkan deskripsi yang bersih
        $matchedFullString = $m[0];
        $description = trim(str_replace($matchedFullString, '', $text));
        $description = preg_replace('/\s+/', ' ', $description);

        return [
            'type' => $type,
            'amount' => $amount,
            'description' => $description ?: ($type === 'income' ? 'Pemasukan' : 'Pengeluaran'),
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
