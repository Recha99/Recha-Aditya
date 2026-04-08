<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use App\Models\Category;
use App\Models\ActivityLog; // Menggunakan Log Yang kita buat sebelumnya
use App\Models\tools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ToolController extends Controller
{
    /**
     * Menampilkan daftar alat (Read).
     */
    public function index()
    {
        // Mengambil data alat, diurutkan terbaru, dengan pagination 10 per halaman
        // 'with' digunakan untuk Eager Loading relasi kategori agar query lebih ringan
        $tools = tools::with('category')->latest()->paginate(10);

        return view('admin.tools.index', compact('tools'));
    }

    /**
     * Menampilkan form tambah alat (Create).
     */
    public function create()
    {
        $categories = Category::all(); // Kita butuh data kategori untuk dropdown
        return view('admin.tools.create', compact('categories'));
    }

    /**
     * Menyimpan data alat baru ke database (Store).
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
            'deskripsi' => 'nullable|string'
        ]);

        // 2. Handle Upload Gambar (Jika ada)
        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            // Simpan di folder: storage/app/public/tools
            $gambarPath = $request->file('gambar')->store('tools', 'public');
        }

        // 3. Simpan ke Database
        tools::create([
            'nama_alat' => $request->nama_alat,
            'category_id' => $request->category_id,
            'stok' => $request->stok,
            'deskripsi' => $request->deskripsi,
            'gambar' => $gambarPath
        ]);

        // 4. Catat Log
        ActivityLog::record('Tambah Alat', 'Menambahkan alat baru: ' . $request->nama_alat);

        return redirect()->route('tools.index')->with('success', 'Alat berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit alat.
     */
    public function edit(tools $tool)
    {
        $categories = Category::all();
        return view('admin.tools.edit', compact('tool', 'categories'));
    }

    /**
     * Memperbarui data alat (Update).
     */
    public function update(Request $request, tools $tool)
    {
        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',


        ]);

        // Cek jika mengubah stok dan ada loan aktif, pastikan stok baru cukup
        if ($request->stok < $tool->loan()->where('status', 'disetujui')->sum('jumlah')) {
            return back()->withErrors(['stok' => 'Stok tidak bisa dikurangi karena ada alat yang sedang dipinjam.'])->withInput();
        }

        $data = $request->except(['gambar']);

        // Handle Ganti Gambar
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($tool->gambar && Storage::disk('public')->exists($tool->gambar)) {
                Storage::disk('public')->delete($tool->gambar);
            }
            // Simpan gambar baru
            $data['gambar'] = $request->file('gambar')->store('tools', 'public');
        }

        $tool->update($data);

        ActivityLog::record('Update Alat', 'Memperbarui data alat: ' . $tool->nama_alat);

        return redirect()->route('tools.index')->with('success', 'Data alat diperbarui.');
    }

    /**
     * Menghapus alat (Delete).
     */
    public function destroy(tools $tool)
    {
        // Cek apakah alat ini sedang dipinjam (status pending atau disetujui)
        if ($tool->loan()->whereIn('status', ['pending', 'disetujui'])->count() > 0) {
            return back()->withErrors(['error' => 'Alat tidak bisa dihapus karena sedang dalam proses peminjaman atau sudah disetujui.']);
        }

        // Hapus file gambar dari storage jika ada
        if ($tool->gambar && Storage::disk('public')->exists($tool->gambar)) {
            Storage::disk('public')->delete($tool->gambar);
        }

        $namaAlat = $tool->nama_alat;

        $tool->delete();

        ActivityLog::record('Hapus Alat', 'Menghapus alat: ' . $namaAlat);

        return redirect()->route('tools.index')->with('success', 'Alat berhasil dihapus.');
    }
}
