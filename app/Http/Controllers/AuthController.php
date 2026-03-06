<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use \App\Traits\HasConfigVersion;

    public function showLoginForm()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'admin' ? redirect()->route('admin.index') : redirect()->route('counter.select');
        }
        return view('auth.login');
    }

    public function showAdminLoginForm()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.index');
        }
        return view('auth.admin-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->role === 'staff') {
                $request->session()->regenerate();
                return redirect()->route('counter.select');
            }

            Auth::logout();
            return back()->withErrors([
                'email' => 'Login ini hanya untuk Staf Loket.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                $request->session()->regenerate();
                return redirect()->route('admin.index');
            }

            Auth::logout();
            return back()->withErrors([
                'email' => 'Hanya Super Admin yang dapat login di sini.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $role = $user ? $user->role : null;

        // Unlock counter if user is at a counter
        if ($user && $user->occupiedCounter && $user->occupiedCounter->occupied_by == $user->id) {
            // Update counter log with logout time
            \App\Models\CounterLog::where('user_id', $user->id)
                ->where('counter_id', $user->occupiedCounter->id)
                ->whereNull('logout_at')
                ->latest()
                ->first()
                    ?->update(['logout_at' => now()]);

            $user->occupiedCounter->update([
                'occupied_by' => null,
                'status' => 'offline',
                'last_seen_at' => null
            ]);
        }

        $this->updateConfigVersion();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($role === 'admin') {
            return redirect()->route('admin.login');
        }

        return redirect()->route('login');
    }
}
