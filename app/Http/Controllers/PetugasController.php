<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Tool;
use App\Models\tools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PetugasController extends Controller
{
    public function index() {
        // Data yang statusnya pending
        $loans = Loan::where('status', 'pending')->with(['user', 'tool'])->get();

        // Data yang statusnya disetujui (sedang dipinjam)
        $activeLoans = Loan::where('status', 'disetujui')->with(['user', 'tool'])->get();

        $sudahDikembalikan = Loan::where('status', 'kembali')->with(['user', 'tool'])->get();

        return view('petugas.dashboard', compact('loans', 'activeLoans', 'sudahDikembalikan'));
    }

    public function approve($id) {
        $loan = Loan::findOrFail($id);
        $loan->update([
            'status' => 'disetujui',
            'petugas_id' => Auth::id()
        ]);

        // Kurangi stok alat sesuai jumlah pinjam
        $tool = tools::findOrFail($loan->tool_id);
        $tool->decrement('stok', $loan->jumlah ?? 1);

        return back()->with('success', 'Peminjaman disetujui.');
    }

    public function reject($id) {
        $loan = Loan::findOrFail($id);
        $loan->update([
            'status' => 'ditolak',
            'petugas_id' => Auth::id()
        ]);

        return back()->with('success', 'Peminjaman ditolak.');
    }

    public function processReturn(Request $request, $id) {
        $request->validate([
            'bukti_foto' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
        ]);

        $loan = Loan::findOrFail($id);

        // Handle upload bukti foto
        $buktiPath = null;
        if ($request->hasFile('bukti_foto')) {
            $buktiPath = $request->file('bukti_foto')->store('bukti_pengembalian', 'public');
        }

        $tgl_kembali = now();

        // Logika Denda
        $selisih = $loan->tanggal_kembali_rencana->diffInDays($tgl_kembali, false); // Selisih hari, negatif jika terlambat
        $denda = 0;

        if ($selisih < 0) { // Jika terlambat
            $hari_terlambat = abs($selisih);
            if ($hari_terlambat > 2) {
                $denda = $hari_terlambat * 5000; // Denda per hari terlambat
            }
        }

        $loan->update([
            'status' => 'kembali',
            'tanggal_kembali_aktual' => $tgl_kembali,
            'bukti_foto' => $buktiPath,
            'total_denda' => $denda
        ]);

        // Kembalikan stok sesuai jumlah pinjam
        $tool = tools::findOrFail($loan->tool_id);
        $tool->increment('stok', $loan->jumlah ?? 1);

        return back()->with('success', 'Alat telah dikembalikan dengan bukti foto. Denda: Rp ' . number_format($denda, 0, ',', '.'));
    }

    public function report(Request $request) {
        // Bisa tambahkan filter tanggal jika mau
        $loans = Loan::with(['user', 'tool'])->get();
        return view('petugas.laporan', compact('loans'));
    }
}
