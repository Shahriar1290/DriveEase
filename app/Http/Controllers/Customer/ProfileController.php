<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = DB::select("SELECT * FROM users WHERE id = ? LIMIT 1", [auth()->id()])[0];
        return view('customer.dashboard.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $userId = auth()->id();

        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'avatar'  => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // Manual unique-email-except-self check
        $emailTaken = DB::select(
            "SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1",
            [$request->email, $userId]
        );
        if (!empty($emailTaken)) {
            return back()->withErrors(['email' => 'This email is already in use.']);
        }

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $current = DB::select("SELECT avatar FROM users WHERE id = ? LIMIT 1", [$userId])[0];
            if ($current->avatar) {
                Storage::disk('public')->delete($current->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        if ($avatarPath) {
            DB::update("
                UPDATE users SET name = ?, email = ?, phone = ?, address = ?, avatar = ?, updated_at = NOW()
                WHERE id = ?
            ", [$request->name, $request->email, $request->phone, $request->address, $avatarPath, $userId]);
        } else {
            DB::update("
                UPDATE users SET name = ?, email = ?, phone = ?, address = ?, updated_at = NOW()
                WHERE id = ?
            ", [$request->name, $request->email, $request->phone, $request->address, $userId]);
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $userId = auth()->id();
        $user   = DB::select("SELECT * FROM users WHERE id = ? LIMIT 1", [$userId])[0];

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        DB::update("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?", [
            Hash::make($request->password),
            $userId,
        ]);

        return back()->with('success', 'Password changed successfully.');
    }
}
