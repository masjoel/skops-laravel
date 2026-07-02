<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Personil;
use Illuminate\Http\Request;

class PersonilController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = Personil::query();
        if ($request->filled('search')) {
            $q->where(function ($query) use ($request) {
                $query->where('nama', 'like', '%' . $request->search . '%')
                    ->orWhere('telepon', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('kota', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('gol')) {
            $q->where('gol', $request->gol);
        }
        $anggota = $q->orderBy('nama')->paginate(20)->withQueryString();
        return view('master.anggota.index', compact('anggota'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
