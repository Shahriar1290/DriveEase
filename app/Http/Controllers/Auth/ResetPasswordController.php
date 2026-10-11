<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function showForm(Request $request, string $token)
    {
        $email = $request->get('email');

        if (!$email) {
            return redirect()->route('password.request')
                ->with('error', 'Invalid password reset request.');
        }

        $record = DB::select(
            "SELECT * FROM password_reset_tokens WHERE email = ? LIMIT 1",
            [$email]
        );

        if (empty($record) || !Hash::check($token, $record[0]->token)) {
            return redirect()->route('password.request')
                ->with('error', 'This password reset token is invalid or has expired.');
        }

        if (now()->diffInMinutes(\Carbon\Carbon::parse($record[0]->created_at)) > 60) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return redirect()->route('password.request')
                ->with('error', 'This password reset token has expired. Please request a new one.');
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $record = DB::select(
            "SELECT * FROM password_reset_tokens WHERE email = ? LIMIT 1",
            [$request->email]
        );

        if (empty($record) || !Hash::check($request->token, $record[0]->token)) {
            return back()->with('error', 'This password reset token is invalid.');
        }

        if (now()->diffInMinutes(\Carbon\Carbon::parse($record[0]->created_at)) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->with('error', 'This password reset token has expired.');
        }

        DB::table('users')
            ->where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')
            ->with('success', 'Your password has been reset. You may now sign in.');
    }
}
