<?php

use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use App\Notifications\TransactionPending;
use App\Notifications\TransactionSuccess;
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

    Route::get('/debts',                 [App\Http\Controllers\Api\DebtController::class, 'index']);
    Route::post('/debts',                [App\Http\Controllers\Api\DebtController::class, 'store']);
    Route::get('/debts/{id}',            [App\Http\Controllers\Api\DebtController::class, 'show']);
    Route::put('/debts/{id}',            [App\Http\Controllers\Api\DebtController::class, 'update']);
    Route::delete('/debts/{id}',         [App\Http\Controllers\Api\DebtController::class, 'destroy']);
    Route::post('/debts/{id}/pay',       [App\Http\Controllers\Api\DebtController::class, 'pay']);

    Route::get('/dashboard/stats',       [App\Http\Controllers\Api\DashboardController::class, 'stats']);
    Route::get('/dashboard/chart',       [App\Http\Controllers\Api\DashboardController::class, 'chart']);
   Route::get('/savings', [App\Http\Controllers\Api\SavingController::class, 'index']);
Route::post('/savings', [App\Http\Controllers\Api\SavingController::class, 'store']);

Route::get('/savings/{id}', [App\Http\Controllers\Api\SavingController::class, 'show']);
Route::put('/savings/{id}', [App\Http\Controllers\Api\SavingController::class, 'update']);
Route::delete('/savings/{id}', [App\Http\Controllers\Api\SavingController::class, 'destroy']);

Route::get('/savings/{id}/deposits', [App\Http\Controllers\Api\SavingController::class, 'deposits']);
Route::post('/savings/{id}/deposits', [App\Http\Controllers\Api\SavingController::class, 'deposit']);
    Route::post('/chat-bot/parse-and-save', [App\Http\Controllers\Api\ChatBotController::class, 'parseAndSave']);


});

function botReply(int $chatId, string $text, string $parseMode = 'Markdown'): void
{
    Http::post(
        "https://api.telegram.org/bot" . config('services.telegram.bot_token') . "/sendMessage",
        [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $parseMode,
        ]
    );
}

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

            botReply($chatId,
                "❌ *Kode integrasi tidak valid*\n\n".
                "Kode yang Anda masukkan tidak ditemukan dalam sistem.\n".
                "Pastikan kode *pairing* yang dimasukkan sudah benar.\n\n".
                "Kode *pairing* dapat ditemukan di halaman *Profil* aplikasi DompetKuu."
            );

            return response()->json(['ok' => true]);
        }

        $user->update([
            'telegram_chat_id' => $chatId,
            'telegram_disconnected_at' => null,
        ]);

        botReply($chatId,
            "✅ *Akun berhasil terhubung!*\n\n".
            "📋 *Akun:* {$user->name}\n".
            "💡 Sekarang kamu bisa mencatat transaksi langsung dari sini.\n\n".
            "Contoh:\n".
            "📈 `masuk 500000 gaji juni`\n".
            "📉 `keluar 15000 beli kopi`\n\n".
            "Butuh bantuan? Ketik /start"
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

        botReply($chatId,
            "⚠️ *Akun belum terhubung*\n\n".
            "Silakan hubungkan akun kamu terlebih dahulu menggunakan kode *pairing*.\n\n".
            "Cara:\n".
            "1. Buka menu *Profil* di aplikasi DompetKuu\n".
            "2. Salin kode *pairing* Telegram\n".
            "3. Ketik: `/connect TG-XXXXXX`\n\n".
            "Contoh: `/connect TG-A1B2C3`"
        );

        return response()->json(['ok' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | START
    |--------------------------------------------------------------------------
    */

    if ($text === '/start') {

        botReply($chatId,
            "👋 *Halo, {$user->name}*\n\n".
            "📌 *Cara Catat Transaksi:*\n".
            "📈 `masuk [jumlah] [keterangan]`\n".
            "📉 `keluar [jumlah] [keterangan]`\n\n".
            "💡 Contoh:\n".
            "✅ `masuk 500000 gaji bulan ini`\n".
            "✅ `keluar 15.000 beli kopi`\n".
            "✅ `masuk Rp 150.000 uang saku`\n\n".
            "📌 *Perintah Lain:*\n".
            "/saldo — Lihat ringkasan keuangan\n".
            "/riwayat — 5 transaksi terbaru\n\n".
            "Kategori akan terdeteksi otomatis."
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

        botReply($chatId,
            "💰 *Ringkasan Keuangan*\n\n".
            "📈 Pemasukan: Rp".number_format($totalIncome, 0, ',', '.')."\n".
            "📉 Pengeluaran: Rp".number_format($totalExpense, 0, ',', '.')."\n".
            "─────────────────────\n".
            "{$balanceIcon} Saldo Bersih: Rp".number_format($netBalance, 0, ',', '.').
            ($netBalance < 0 ? "\n⚠️ Saldo negatif!" : "").
            "\n\n📊 *Bulan Ini*\n".
            "   Pemasukan: Rp".number_format($monthIncome, 0, ',', '.')."\n".
            "   Pengeluaran: Rp".number_format($monthExpense, 0, ',', '.')."\n".
            "   Sisa: Rp".number_format($monthIncome - $monthExpense, 0, ',', '.').
            $topCategoryText.
            "\n\n📋 Total {$totalCount} transaksi tercatat"
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
            botReply($chatId,
                "📭 *Belum ada transaksi*\n\n".
                "Belum ada transaksi yang tercatat. Mulai catat transaksi kamu:\n".
                "📈 `masuk 500000 gaji`\n".
                "📉 `keluar 15000 bakso`"
            );
        } else {
            $reply = "📋 *Riwayat Transaksi Terbaru*\n\n";
            foreach ($riwayat as $t) {
                $reply .= "{$t['icon']} *{$t['type']}*\n";
                $reply .= "   Rp".number_format($t['amount'], 0, ',', '.')." | {$t['category']}\n";
                $reply .= "   🗓 {$t['date']}\n\n";
            }
            $reply .= "💡 Ketik /saldo untuk cek saldo lengkap";

            botReply($chatId, $reply);
        }

        return response()->json(['ok' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | TRANSAKSI
    |--------------------------------------------------------------------------
    */

    if (preg_match('/^(masuk|keluar)\s+(?:Rp|rp)?\s*([0-9.]+)\s*(.*)$/iu', $text, $matches)) {

        $action = strtolower($matches[1]);
        $amount = (float) str_replace('.', '', $matches[2]);
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

            $user->notify(new TransactionSuccess(
                type: 'Pemasukan',
                amount: $amount,
                category: $category?->name ?? '-',
                description: $description,
                source: 'Telegram',
            ));

            botReply($chatId,
                "✅ *Pemasukan berhasil dicatat*\n\n".
                "💰 Rp".number_format($amount, 0, ',', '.')."\n".
                "📂 {$category?->name}\n".
                "📝 {$description}"
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

        $user->notify(new TransactionSuccess(
            type: 'Pengeluaran',
            amount: $amount,
            category: $category?->name ?? '-',
            description: $description,
            source: 'Telegram',
        ));

        if ($status === 'pending' && $user->parent_id) {
            $user->parent?->notify(new TransactionPending(
                childName: $user->name,
                type: 'Pengeluaran',
                amount: $amount,
                description: $description,
            ));
        }

        botReply($chatId,
            ($status === 'pending'
                ? "⏳ *Pengeluaran diajukan ke Orang Tua*\n\n"
                : "✅ *Pengeluaran berhasil dicatat*\n\n").
            "💰 Rp".number_format($amount, 0, ',', '.')."\n".
            "📂 {$category?->name}\n".
            "📝 {$description}".
            ($status === 'pending' ? "\n\n⏳ Menunggu persetujuan Orang Tua..." : "")
        );

        return response()->json(['ok' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT SALAH
    |--------------------------------------------------------------------------
    */

    botReply($chatId,
        "⚠️ *Format tidak sesuai*\n\n".
        "Gunakan format:\n".
        "📈 `masuk [jumlah] [keterangan]`\n".
        "📉 `keluar [jumlah] [keterangan]`\n\n".
        "Contoh benar:\n".
        "✅ `masuk 500000 gaji`\n".
        "✅ `keluar 15.000 bakso`\n".
        "✅ `masuk Rp 150.000 uang saku`\n".
        "✅ `keluar rp 20000 bensin`\n\n".
        "Ketik /start untuk bantuan lebih lanjut."
    );

    return response()->json(['ok' => true]);
});
