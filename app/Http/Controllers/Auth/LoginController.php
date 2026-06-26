<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Raw SQL lookup instead of Auth::attempt() against Eloquent
        $row = DB::select("SELECT * FROM users WHERE email = ? LIMIT 1", [$request->email]);

        if (empty($row) || !Hash::check($request->password, $row[0]->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        $userRow = $row[0];

        if (!$userRow->is_active) {
            return back()->with('error', 'Your account has been deactivated. Please contact support.');
        }

        // Laravel's session guard requires an Eloquent Authenticatable instance.
        // We hydrate the minimal User model from the raw row we already fetched
        // (no second query) purely so Auth::login() can store it in the session.
        $user = new User();
        $user->forceFill((array) $userRow);
        $user->exists = true;

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return $userRow->role === 'admin'
            ? redirect()->intended(route('admin.dashboard'))
            : redirect()->intended(route('customer.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'You have been logged out.');
    }
}
