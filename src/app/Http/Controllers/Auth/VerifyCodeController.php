<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetCode;
use Illuminate\Http\Request;

class VerifyCodeController extends Controller
{
    public function show()
    {
        return view('auth.verify-code');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|string|size:6',
        ]);

        $record = PasswordResetCode::where('email', $request->email)
            ->where('code', $request->code)
            ->where('is_used', false)
            ->where('expired_at', '>', now())
            ->first();

        if (!$record) {
            return back()->withErrors(['code' => 'Kode tidak valid atau sudah kadaluarsa.']);
        }

        $record->update(['is_used' => true]);

        return redirect()->route('password.reset', [
            'token' => $record->code,
            'email' => $request->email,
        ]);
    }
}
