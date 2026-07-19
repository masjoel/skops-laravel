<?php

namespace App\Http\Controllers\Seting;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Murid;
use App\Models\OrangTua;
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

        // Data diembed langsung ke view — tidak ada AJAX/JSON request terpisah
        $guruList = Guru::with('personil:id,nama,email')
            ->get(['id', 'personil_id', 'nip'])
            ->map(fn($g) => [
                'id'    => $g->personil_id,   // personil.id — untuk disimpan ke users.personil_id
                'nama'  => $g->personil?->nama ?? '',
                'email' => $g->personil?->email ?? '',
                'nip'   => $g->nip ?? '',
            ]);

        $muridList = Murid::with('personil:id,nama,email')
            ->get(['id', 'personil_id', 'nis'])
            ->map(fn($m) => [
                'id'    => $m->personil_id,
                'nama'  => $m->personil?->nama ?? '',
                'email' => $m->personil?->email ?? '',
                'nis'   => $m->nis ?? '',
            ]);

        $orangTuaList = OrangTua::with('personil:id,nama,email')
            ->get(['id', 'personil_id'])
            ->map(fn($o) => [
                'id'    => $o->personil_id,
                'nama'  => $o->personil?->nama ?? '',
                'email' => $o->personil?->email ?? '',
            ]);

        return view('seting.user.create', compact('roles', 'guruList', 'muridList', 'orangTuaList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:20|unique:users,username',
            'name' => 'required|string|max:100',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:administrator,guru,murid,orang_tua,operator',
            'status' => 'required',
            'email' => 'nullable|email|max:100',
        ]);

        $rolesWithPersonil = ['guru', 'murid', 'orang_tua'];
        $personilId = in_array($request->role, $rolesWithPersonil)
            ? ($request->input('linked_personil_id') ?: null)
            : null;
        // dd($personilId);

        User::create([
            'personil_id' => $personilId,
            'username' => $request->username,
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status,
            'email' => $request->email,
        ]);

        return redirect()->route('seting.user.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user  = User::findOrFail($id);
        $roles = User::ROLES;

        $guruList = Guru::with('personil:id,nama,email')
            ->get(['id', 'personil_id', 'nip'])
            ->map(fn($g) => [
                'id'    => $g->personil_id,
                'nama'  => $g->personil?->nama ?? '',
                'email' => $g->personil?->email ?? '',
                'nip'   => $g->nip ?? '',
            ]);

        $muridList = Murid::with('personil:id,nama,email')
            ->get(['id', 'personil_id', 'nis'])
            ->map(fn($m) => [
                'id'    => $m->personil_id,
                'nama'  => $m->personil?->nama ?? '',
                'email' => $m->personil?->email ?? '',
                'nis'   => $m->nis ?? '',
            ]);

        $orangTuaList = OrangTua::with('personil:id,nama,email')
            ->get(['id', 'personil_id'])
            ->map(fn($o) => [
                'id'    => $o->personil_id,
                'nama'  => $o->personil?->nama ?? '',
                'email' => $o->personil?->email ?? '',
            ]);

        return view('seting.user.edit', compact('user', 'roles', 'guruList', 'muridList', 'orangTuaList'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $id . ',id',
            'name' => 'required|string|max:100',
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:administrator,guru,murid,orang_tua,operator',
            'status' => 'required',
            'email' => 'nullable|email|max:100',
        ]);

        $rolesWithPersonil = ['guru', 'murid', 'orang_tua'];
        $personilId = in_array($request->role, $rolesWithPersonil)
            ? ($request->input('linked_personil_id') ?: null)
            : null;

        $data = $request->only(['username', 'name', 'role', 'status', 'email']);
        $data['personil_id'] = $personilId;

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
