<?php

namespace App\Http\Controllers;

use App\Models\Tarif;
use Illuminate\Http\Request;

class TarifController extends Controller
{
    public function index()
    {
        $tarifs = Tarif::orderBy('kategori', 'asc')->get();
        return view('tarifs.index', compact('tarifs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori' => ['required', 'string', 'max:255', 'unique:tarifs,kategori'],
            'tarif_berhasil' => ['required', 'integer', 'min:0'],
            'tarif_gagal' => ['nullable', 'integer', 'min:0'],
        ]);

        Tarif::create([
            'kategori' => $validated['kategori'],
            'tarif_berhasil' => $validated['tarif_berhasil'],
            'tarif_gagal' => $request->filled('tarif_gagal') ? (int)$validated['tarif_gagal'] : null,
        ]);

        return redirect()->route('tarifs.index')->with('success', 'Kategori tarif baru berhasil ditambahkan!');
    }

    public function update(Request $request, Tarif $tarif)
    {
        $validated = $request->validate([
            'kategori' => ['required', 'string', 'max:255', 'unique:tarifs,kategori,' . $tarif->id],
            'tarif_berhasil' => ['required', 'integer', 'min:0'],
            'tarif_gagal' => ['nullable', 'integer', 'min:0'],
        ]);

        $tarif->update([
            'kategori' => $validated['kategori'],
            'tarif_berhasil' => $validated['tarif_berhasil'],
            'tarif_gagal' => $request->filled('tarif_gagal') ? (int)$validated['tarif_gagal'] : null,
        ]);

        return redirect()->route('tarifs.index')->with('success', 'Tarif berhasil diperbarui!');
    }

    public function destroy(Tarif $tarif)
    {
        $tarif->delete();
        return redirect()->route('tarifs.index')->with('success', 'Kategori tarif berhasil dihapus!');
    }
}
