<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'username' => [
                'required', 
                'string', 
                'max:50', 
                Rule::unique('users', 'username')->ignore($user->id),
                'not_in:admin,administrator,superadmin,root'
            ],
            'name'     => 'required|string|max:100',
            'email'    => 'nullable|email|max:100',
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'username.not_in' => 'Username sudah digunakan.'
        ]);

        $data = $request->only(['username', 'name', 'email']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
