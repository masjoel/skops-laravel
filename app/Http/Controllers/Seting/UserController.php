<?php

namespace App\Http\Controllers\Seting;

use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = UserModel::query();
        if ($request->filled('search')) {
            $q->where(function ($query) use ($request) {
                $query->where('nama', 'like', '%'.$request->search.'%')
                      ->orWhere('username', 'like', '%'.$request->search.'%');
            });
        }
        if ($request->filled('level')) {
            $q->where('level', $request->level);
        }
        $users = $q->orderBy('nama')->paginate(20)->withQueryString();
        return view('seting.user.index', compact('users'));
    }

    public function create()
    {
        return view('seting.user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:user,username',
            'nama'     => 'required|string|max:100',
            'password' => 'required|string|min:6|confirmed',
            'level'    => 'required|in:administrator,operator,kasir',
            'status'   => 'required|in:Aktif,Nonaktif',
            'email'    => 'nullable|email|max:100',
        ]);

        UserModel::create([
            'username' => $request->username,
            'nama'     => $request->nama,
            'password' => Hash::make($request->password),
            'level'    => $request->level,
            'status'   => $request->status,
            'email'    => $request->email,
        ]);

        return redirect()->route('seting.user.index')
                         ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = UserModel::findOrFail($id);
        return view('seting.user.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = UserModel::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:50|unique:user,username,' . $id . ',idx',
            'nama'     => 'required|string|max:100',
            'password' => 'nullable|string|min:6|confirmed',
            'level'    => 'required|in:administrator,operator,kasir',
            'status'   => 'required|in:Aktif,Nonaktif',
            'email'    => 'nullable|email|max:100',
        ]);

        $data = $request->only(['username', 'nama', 'level', 'status', 'email']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('seting.user.index')
                         ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = UserModel::findOrFail($id);

        if (Auth::id() == $id) {
            return redirect()->route('seting.user.index')
                             ->with('error', 'Tidak dapat menghapus akun yang sedang aktif.');
        }

        $user->delete();

        return redirect()->route('seting.user.index')
                         ->with('success', 'User berhasil dihapus.');
    }

    public function show($id) { return redirect()->route('seting.user.index'); }
}
