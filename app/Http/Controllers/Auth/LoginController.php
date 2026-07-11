<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        $Sekolah = Sekolah::first();
        return view('auth.login', compact('Sekolah'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::where('username', $request->username)
            ->where('status', true)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['login' => 'Username atau password salah.'])->withInput();
        }

        $Sekolah = Sekolah::first();
        // $pending    = \App\Models\Barang::whereColumn('stok', '<=', 'stok_kritis')->count();

        Auth::login($user);

        session([
            'logged_in' => true,
            'IDuser'    => $user->id,
            'username'  => $user->username,
            'namaopr'   => $user->name,
            'level'     => $user->level,
            'photo'     => $user->photo,
            'status'    => $user->status,
            'LOGO'      => $Sekolah?->logo,
            'jdigit'    => $Sekolah?->jdigit,
            // 'brgpend'   => $pending,
            'linkseg'   => in_array($user->level, ['administrator', 'operator']) ? 'seting' : 'dashboard',
        ]);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }
}
