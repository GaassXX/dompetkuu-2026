<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function show()
    {
        return view('auth.forgot-password');
    }

    public function send(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        PasswordResetCode::create([
            'email'      => $request->email,
            'code'       => $code,
            'expired_at' => now()->addMinutes(10),
            'is_used'    => false,
        ]);

        Mail::raw(
            "Kode verifikasi reset password Anda: $code\n\nKode berlaku selama 10 menit.\n\nJika Anda tidak meminta reset password, abaikan email ini.",
            function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('Kode Reset Password - DompetKuu');
            }
        );

        return redirect()->route('password.verify')
            ->with('reset_email', $request->email);
    }
}
