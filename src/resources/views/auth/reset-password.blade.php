<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>dompettkuu — Reset Kata Sandi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #f7f9f8; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-md">

        {{-- Logo --}}
        <div class="flex items-center justify-center gap-2 mb-8">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                 style="background:linear-gradient(135deg,#059669,#10b981);">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M21 18v1a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1h-9a2 2 0 00-2 2v8a2 2 0 002 2h9zm-9-2h10V8H12v8zm4-2.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
                </svg>
            </div>
            <span class="text-lg font-extrabold text-gray-800">dompet<span class="text-emerald-600">tkuu</span></span>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">

            <h1 class="text-2xl font-extrabold text-gray-900 mb-1">Reset kata sandi</h1>
            <p class="text-sm text-gray-500 mb-6">Buat kata sandi baru untuk akun Anda.</p>

            {{-- Alert error --}}
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 text-xs rounded-xl px-3.5 py-3 mb-5 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Email</label>
                    <input type="email" name="email" value="{{ $email ?? old('email') }}"
                        class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-500 cursor-not-allowed focus:outline-none"
                        readonly>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kata Sandi Baru</label>
                    <input type="password" name="password"
                        placeholder="Minimal 8 karakter"
                        class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition {{ $errors->has('password') ? 'border-red-400' : '' }}"
                        required minlength="8">
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation"
                        placeholder="Ulangi kata sandi baru"
                        class="w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition"
                        required minlength="8">
                </div>

                <button type="submit"
                    class="w-full text-white font-semibold py-3.5 rounded-xl transition duration-200 text-sm shadow-lg shadow-emerald-600/20 hover:shadow-emerald-600/30 hover:-translate-y-0.5"
                    style="background: linear-gradient(135deg, #059669, #10b981);">
                    Reset Kata Sandi
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            &copy; {{ date('Y') }} dompettkuu. Semua hak dilindungi.
        </p>
    </div>

</body>
</html>
