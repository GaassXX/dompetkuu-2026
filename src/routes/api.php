<?php

use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API DompetKuu
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('/login',   App\Http\Controllers\Api\Auth\LoginController::class);
    Route::post('/register', App\Http\Controllers\Api\Auth\RegisterController::class);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout',  [App\Http\Controllers\Api\Auth\LoginController::class, 'logout']);
        Route::get('/me',       App\Http\Controllers\Api\Auth\ProfileController::class);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/transactions',          [App\Http\Controllers\Api\TransactionController::class, 'index']);
    Route::post('/transactions',         [App\Http\Controllers\Api\TransactionController::class, 'store']);
    Route::get('/transactions/{id}',     [App\Http\Controllers\Api\TransactionController::class, 'show']);
    Route::put('/transactions/{id}',     [App\Http\Controllers\Api\TransactionController::class, 'update']);
    Route::delete('/transactions/{id}',  [App\Http\Controllers\Api\TransactionController::class, 'destroy']);

    Route::get('/categories',            [App\Http\Controllers\Api\CategoryController::class, 'index']);

    Route::get('/dashboard/stats',       [App\Http\Controllers\Api\DashboardController::class, 'stats']);
    Route::get('/dashboard/chart',       [App\Http\Controllers\Api\DashboardController::class, 'chart']);
});

Route::post('/telegram/webhook', function (Request $request) {

    Log::info('TELEGRAM', $request->all());

    $message = $request->input('message');

    if (! $message) {
        return response()->json(['ok' => true]);
    }

    $chatId = $message['chat']['id'];
    $text = trim($message['text'] ?? '');

    /*
    |--------------------------------------------------------------------------
    | CONNECT TELEGRAM
    |--------------------------------------------------------------------------
    */

    if (preg_match('/^\/connect\s+(.+)$/i', $text, $matches)) {

        $pairCode = strtoupper(trim($matches[1]));

        $user = User::where('telegram_pair_code', $pairCode)->first();

        if (! $user) {

            Http::post(
                "https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/sendMessage",
                [
                    'chat_id' => $chatId,
                    'text' => '❌ Kode integrasi tidak ditemukan.',
                ]
            );

            return response()->json(['ok' => true]);
        }

        $user->update([
            'telegram_chat_id' => $chatId,
            'telegram_disconnected_at' => null,
        ]);

        Http::post(
            "https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/sendMessage",
            [
                'chat_id' => $chatId,
                'text' => "✅ Telegram berhasil terhubung ke akun:\n{$user->name}",
            ]
        );

        return response()->json(['ok' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | CEK USER
    |--------------------------------------------------------------------------
    */

    $user = User::where('telegram_chat_id', $chatId)->first();

    if (! $user) {

        Http::post(
            "https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/sendMessage",
            [
                'chat_id' => $chatId,
                'text' => "❌ Telegram belum terhubung.\n\nGunakan:\n/connect TG-XXXXXX",
            ]
        );

        return response()->json(['ok' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | START
    |--------------------------------------------------------------------------
    */

    if ($text === '/start') {

        Http::post(
            "https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/sendMessage",
            [
                'chat_id' => $chatId,
                'text' =>
                "👋 Halo {$user->name}\n\n".
                "📌 *Perintah:*\n".
                "📈 masuk 50000 uangsaku dari ayah\n".
                "📉 keluar 15000 beli kopi\n".
                "/saldo — Cek saldo bersih\n".
                "/riwayat — 5 transaksi terakhir\n\n".
                "Kategori akan dideteksi otomatis.",
            ]
        );

        return response()->json(['ok' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | SALDO
    |--------------------------------------------------------------------------
    */

    if ($text === '/saldo') {

        $totalIncome = Income::where('user_id', $user->id)
            ->where('status', 'approved')
            ->sum('amount');

        $totalExpense = Expense::where('user_id', $user->id)
            ->where('status', 'approved')
            ->sum('amount');

        $netBalance = $totalIncome - $totalExpense;

        $monthIncome = Income::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        $monthExpense = Expense::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        $topCategory = Expense::where('user_id', $user->id)
            ->where('status', 'approved')
            ->selectRaw('category_id, sum(amount) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->first();

        $topCategoryText = '';
        if ($topCategory && $topCategory->category_id && $totalExpense > 0) {
            $cat = Category::find($topCategory->category_id);
            $pct = round(($topCategory->total / $totalExpense) * 100);
            $topCategoryText =
                "\n\n🏷 *Kategori Pengeluaran Terbesar*\n".
                "   {$cat?->name}: Rp".number_format($topCategory->total, 0, ',', '.')." ({$pct}%)";
        }

        $totalCount = Income::where('user_id', $user->id)->where('status', 'approved')->count()
            + Expense::where('user_id', $user->id)->where('status', 'approved')->count();

        $balanceIcon = $netBalance >= 0 ? '💵' : '🔴';

        Http::post(
            "https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/sendMessage",
            [
                'chat_id' => $chatId,
                'text' =>
                    "💰 *Saldo Keuangan Anda*\n\n".
                    "📈 Total Pemasukan: Rp".number_format($totalIncome, 0, ',', '.')."\n".
                    "📉 Total Pengeluaran: Rp".number_format($totalExpense, 0, ',', '.')."\n".
                    "─────────────────────\n".
                    "{$balanceIcon} Saldo Bersih: Rp".number_format($netBalance, 0, ',', '.').
                    ($netBalance < 0 ? "\n⚠️ Saldo negatif!" : "").
                    "\n\n📊 *Ringkasan Bulan Ini*\n".
                    "   Pemasukan: Rp".number_format($monthIncome, 0, ',', '.')."\n".
                    "   Pengeluaran: Rp".number_format($monthExpense, 0, ',', '.')."\n".
                    "   Sisa: Rp".number_format($monthIncome - $monthExpense, 0, ',', '.').
                    $topCategoryText.
                    "\n\n📋 Total {$totalCount} transaksi",
                'parse_mode' => 'Markdown',
            ]
        );

        return response()->json(['ok' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | RIWAYAT
    |--------------------------------------------------------------------------
    */

    if ($text === '/riwayat') {

        $incomes = Income::where('user_id', $user->id)
            ->where('status', 'approved')
            ->with('category')
            ->latest('date')
            ->take(5)
            ->get()
            ->map(fn($i) => [
                'date' => $i->date->format('d/m/Y'),
                'icon' => '📈',
                'type' => 'Pemasukan',
                'category' => $i->category?->name ?? '-',
                'amount' => $i->amount,
            ]);

        $expenses = Expense::where('user_id', $user->id)
            ->where('status', 'approved')
            ->with('category')
            ->latest('date')
            ->take(5)
            ->get()
            ->map(fn($e) => [
                'date' => $e->date->format('d/m/Y'),
                'icon' => '📉',
                'type' => 'Pengeluaran',
                'category' => $e->category?->name ?? '-',
                'amount' => $e->amount,
            ]);

        $riwayat = $incomes->concat($expenses)
            ->sortByDesc('date')
            ->take(5);

        if ($riwayat->isEmpty()) {
            $reply = "📭 Belum ada transaksi approved.";
        } else {
            $reply = "📋 *Riwayat Transaksi Terbaru*\n\n";
            foreach ($riwayat as $t) {
                $reply .= "✅ {$t['icon']} *{$t['type']}*\n";
                $reply .= "   Rp".number_format($t['amount'], 0, ',', '.')." | {$t['category']}\n";
                $reply .= "   🗓 {$t['date']}\n\n";
            }
        }

        Http::post(
            "https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/sendMessage",
            [
                'chat_id' => $chatId,
                'text' => $reply,
                'parse_mode' => 'Markdown',
            ]
        );

        return response()->json(['ok' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | TRANSAKSI
    |--------------------------------------------------------------------------
    */

    if (preg_match('/^(masuk|keluar)\s+(\d+)\s*(.*)$/i', $text, $matches)) {

        $action = strtolower($matches[1]);
        $amount = (float) $matches[2];
        $description = trim($matches[3]);

        $type = $action === 'masuk'
            ? 'income'
            : 'expense';

        /*
        |--------------------------------------------------------------------------
        | KEYWORD KATEGORI
        |--------------------------------------------------------------------------
        */

        $keywords = [

            'income' => [
                'Gaji' => ['gaji', 'gajian', 'salary'],
                'Uang Saku' => ['uangsaku', 'uang saku'],
                'Hadiah' => ['hadiah'],
                'Bonus' => ['bonus'],
                'Investasi' => ['investasi'],
                'Penjualan' => ['jual', 'penjualan'],
            ],

            'expense' => [
                'Makan & Minum' => [
                    'kopi',
                    'makan',
                    'minum',
                    'bakso',
                    'nasi',
                    'ayam',
                    'mie',
                    'jajan',
                ],

                'Transportasi' => [
                    'gojek',
                    'grab',
                    'bensin',
                    'transport',
                    'parkir',
                ],

                'Top Up Game' => [
                    'topup',
                    'top up',
                    'ml',
                    'mobile legend',
                    'free fire',
                    'ff',
                    'valorant',
                ],

                'Belanja' => [
                    'belanja',
                    'beli',
                    'alfamart',
                    'indomaret',
                ],

                'Pendidikan' => [
                    'sekolah',
                    'buku',
                    'kuliah',
                    'kursus',
                ],

                'Kesehatan' => [
                    'obat',
                    'dokter',
                    'rumah sakit',
                ],

                'Hiburan' => [
                    'bioskop',
                    'nongkrong',
                    'hiburan',
                ],

                'Tagihan' => [
                    'listrik',
                    'wifi',
                    'air',
                    'internet',
                ],
            ],

        ];

        $categoryName = 'Lainnya';

        foreach ($keywords[$type] as $name => $items) {

            foreach ($items as $keyword) {

                if (str_contains(strtolower($description), strtolower($keyword))) {

                    $categoryName = $name;

                    $description = trim(
                        preg_replace(
                            '/'.preg_quote($keyword, '/').'/i',
                            '',
                            $description
                        )
                    );

                    break 2;
                }
            }
        }

        $category = Category::where('type', $type)
            ->where('name', $categoryName)
            ->first();

        $description = $description ?: '-';

        /*
        |--------------------------------------------------------------------------
        | PEMASUKAN
        |--------------------------------------------------------------------------
        */

        if ($action === 'masuk') {

            Income::create([
                'user_id' => $user->id,
                'category_id' => $category?->id,
                'amount' => $amount,
                'description' => $description,
                'date' => now(),
                'status' => 'approved',
            ]);

            Http::post(
                "https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/sendMessage",
                [
                    'chat_id' => $chatId,
                    'text' =>
                        "✅ Pemasukan berhasil dicatat.\n\n".
                        "💰 Rp".number_format($amount, 0, ',', '.')."\n".
                        "📂 {$category?->name}\n".
                        "📝 {$description}",
                ]
            );

            return response()->json(['ok' => true]);
        }

        /*
        |--------------------------------------------------------------------------
        | PENGELUARAN
        |--------------------------------------------------------------------------
        */

        $status = $user->parent_id
            ? 'pending'
            : 'approved';

        Expense::create([
            'user_id' => $user->id,
            'category_id' => $category?->id,
            'amount' => $amount,
            'description' => $description,
            'date' => now(),
            'status' => $status,
        ]);

        Http::post(
            "https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/sendMessage",
            [
                'chat_id' => $chatId,
                'text' =>
                    ($status === 'pending'
                        ? "⏳ Pengeluaran diajukan.\n\n"
                        : "✅ Pengeluaran berhasil dicatat.\n\n").
                    "💰 Rp".number_format($amount, 0, ',', '.')."\n".
                    "📂 {$category?->name}\n".
                    "📝 {$description}",
            ]
        );

        return response()->json(['ok' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT SALAH
    |--------------------------------------------------------------------------
    */

    Http::post(
        "https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/sendMessage",
        [
            'chat_id' => $chatId,
            'text' =>
                "❓ Format tidak dikenali.\n\n".
                "Contoh:\n".
                "masuk 50000 uangsaku dari ayah\n".
                "keluar 15000 beli kopi",
        ]
    );

    return response()->json(['ok' => true]);
});
