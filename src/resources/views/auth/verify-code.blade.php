<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DompetKuu — Verifikasi Kode</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #FFFDF9; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-md">

        <div class="flex items-center justify-center gap-2 mb-8">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                 style="background:linear-gradient(135deg,#F59E0B,#EA580C);">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M21 18v1a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1h-9a2 2 0 00-2 2v8a2 2 0 002 2h9zm-9-2h10V8H12v8zm4-2.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
                </svg>
            </div>
            <span class="text-lg font-extrabold text-[#1E293B]">Dompet<span class="text-[#F59E0B]">Kuu</span></span>
        </div>

        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">

            <h1 class="text-2xl font-extrabold text-gray-900 mb-1">Cek email Anda</h1>
            <p class="text-sm text-gray-500 mb-6">Kami telah mengirim kode verifikasi ke <strong class="text-gray-700">{{ session('reset_email') ?? old('email') }}</strong></p>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 text-xs rounded-xl px-3.5 py-3 mb-5 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.verify') }}">
                @csrf

                <input type="hidden" name="email" value="{{ session('reset_email') ?? old('email') }}">

                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kode Verifikasi</label>
                    <input type="text" name="code" inputmode="numeric" maxlength="6" autocomplete="one-time-code"
                        placeholder="Masukkan kode 6 digit"
                        class="w-full text-center text-2xl tracking-[0.5em] px-4 py-4 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition {{ $errors->has('code') ? 'border-red-400' : '' }}"
                        required autofocus>
                </div>

                <button type="submit"
                    class="w-full text-white font-semibold py-3.5 rounded-xl transition duration-200 text-sm shadow-lg shadow-amber-600/20 hover:shadow-amber-600/30 hover:-translate-y-0.5"
                    style="background: linear-gradient(135deg, #F59E0B, #EA580C);">
                    Verifikasi Kode
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('password.request') }}" class="text-sm text-amber-600 font-semibold hover:underline">
                    Kirim ulang kode
                </a>
            </div>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            &copy; {{ date('Y') }} DompetKuu. Semua hak dilindungi.
        </p>
    </div>

</body>
</html>
