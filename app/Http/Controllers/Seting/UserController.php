<?php

namespace App\Http\Controllers\Seting;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = User::query();
        if ($request->filled('search')) {
            $q->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('username', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('role')) {
            $q->where('role', $request->role);
        }
        $users = $q->orderBy('name')->paginate(20)->withQueryString();
        $roles = User::ROLES;

        return view('seting.user.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = User::ROLES;
        return view('seting.user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'name'     => 'required|string|max:100',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:administrator,guru,murid,orang_tua,operator',
            'status'   => 'required',
            'email'    => 'nullable|email|max:100',
        ]);

        User::create([
            'username' => $request->username,
            'name'     => $request->name,
            'password' => Hash::make($request->password),
            'role'    => $request->role,
            'status'   => $request->status,
            'email'    => $request->email,
        ]);

        return redirect()->route('seting.user.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = User::ROLES;
        return view('seting.user.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $id . ',id',
            'name'     => 'required|string|max:100',
            'password' => 'nullable|string|min:6|confirmed',
            'role'     => 'required|in:administrator,guru,murid,orang_tua,operator',
            'status'   => 'required',
            'email'    => 'nullable|email|max:100',
        ]);

        $data = $request->only(['username', 'name', 'role', 'status', 'email']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('seting.user.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (Auth::id() == $id) {
            return redirect()->route('seting.user.index')
                ->with('error', 'Tidak dapat menghapus akun yang sedang aktif.');
        }

        $user->delete();

        return redirect()->route('seting.user.index')
            ->with('success', 'User berhasil dihapus.');
    }

    public function show($id)
    {
        return redirect()->route('seting.user.index');
    }
}
