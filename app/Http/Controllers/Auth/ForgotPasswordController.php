<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = DB::select("SELECT id FROM users WHERE email = ? LIMIT 1", [$request->email]);

        if (!empty($user)) {
            $token = Str::random(64);

            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            DB::table('password_reset_tokens')->insert([
                'email'      => $request->email,
                'token'      => Hash::make($token),
                'created_at' => now(),
            ]);

            // In production, send email with the reset link:
            // $resetUrl = route('password.reset', $token) . '?email=' . urlencode($request->email);
            // Mail::to($request->email)->send(new ResetPasswordNotification($resetUrl));

            // For development: log the token so it can be used manually
            \Log::info("Password reset token for {$request->email}: {$token}");
            \Log::info("Reset URL: " . route('password.reset', $token) . '?email=' . urlencode($request->email));
        }

        return back()->with('status', 'If an account with that email exists, a password reset link has been sent.');
    }
}
