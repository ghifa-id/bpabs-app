<?php

namespace App\Http\Controllers\Superuser;

use App\Http\Controllers\Controller;
use App\Models\Tarif;
use Illuminate\Http\Request;

class TarifController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tarifs = Tarif::all();
        return view('pages.superuser.tarif.index', compact('tarifs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.superuser.tarif.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_tarif' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        Tarif::create([
            'nama_tarif' => $request->nama_tarif,
            'harga' => $request->harga,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('superuser.tarif.index')->with('success', 'Tarif berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tarif $tarif)
    {
        return view('pages.superuser.tarif.show', compact('tarif'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tarif $tarif)
    {
        return view('pages.superuser.tarif.edit', compact('tarif'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tarif $tarif)
    {
        $request->validate([
            'nama_tarif' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        $tarif->update([
            'nama_tarif' => $request->nama_tarif,
            'harga' => $request->harga,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('superuser.tarif.index')->with('success', 'Tarif berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Tarif $tarif)
    {
        return view('pages.superuser.tarif.delete', compact('tarif'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tarif $tarif)
    {
        $tarif->delete();
        return redirect()->route('superuser.tarif.index')->with('success', 'Tarif berhasil dihapus');
    }
}