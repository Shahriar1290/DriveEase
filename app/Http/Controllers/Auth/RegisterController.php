<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email',
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|min:8|confirmed',
        ]);

        // Manual uniqueness check (raw SQL instead of unique: validation rule on Eloquent table)
        $exists = DB::select("SELECT id FROM users WHERE email = ?", [$request->email]);
        if (!empty($exists)) {
            return back()->withErrors(['email' => 'This email is already registered.'])->withInput();
        }

        $userId = DB::insert("
            INSERT INTO users (name, email, phone, password, role, is_active, email_verified_at, created_at, updated_at)
            VALUES (?, ?, ?, ?, 'customer', 1, NOW(), NOW(), NOW())
        ", [
            $request->name,
            $request->email,
            $request->phone,
            Hash::make($request->password),
        ]);
        $userId = DB::getPdo()->lastInsertId();
        $user = User::find($userId);
        Auth::login($user);

        return redirect()->route('customer.dashboard')->with('success', 'Welcome! Your account has been created.');
    }
}
