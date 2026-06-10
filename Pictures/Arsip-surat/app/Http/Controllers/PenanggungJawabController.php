<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PenanggungJawab;

class PenanggungJawabController extends Controller
{
    public function index()
{
    $data = PenanggungJawab::where('user_id', auth()->id())
        ->latest()
        ->get();

    return view('penanggung_jawab.index', compact('data'));
}

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'jabatan' => 'required'
        ]);

        PenanggungJawab::create([
            'user_id' => auth()->id(),
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
        ]);

        return redirect()->back()->with('success', 'Data berhasil ditambahkan');
    }

    public function destroy($id)
{
    $data = PenanggungJawab::where('user_id', auth()->id())
        ->findOrFail($id);

    $data->delete();

    return redirect()
        ->back()
        ->with('success', 'Data berhasil dihapus');
}
}