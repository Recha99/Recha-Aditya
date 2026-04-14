<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
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

        // Data yang menunggu konfirmasi pengembalian
        $waitingConfirmation = Loan::where('status', 'menunggu_konfirmasi')->with(['user', 'tool'])->get();

        $sudahDikembalikan = Loan::where('status', 'kembali')->with(['user', 'tool'])->latest()->get();

        return view('petugas.dashboard', compact('loans', 'activeLoans', 'waitingConfirmation', 'sudahDikembalikan'));
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

        ActivityLog::record('Approve Loan', 'Menyetujui peminjaman alat: ' . $tool->nama_alat);

        return back()->with('success', 'Peminjaman disetujui.');
    }

    public function reject($id) {
        $loan = Loan::findOrFail($id);
        $tool = tools::findOrFail($loan->tool_id);
        $loan->update([
            'status' => 'ditolak',
            'petugas_id' => Auth::id()
        ]);

        ActivityLog::record('Reject Loan', 'Menolak peminjaman alat: ' . $tool->nama_alat);

        return back()->with('success', 'Peminjaman ditolak.');
    }

    public function processReturn(Request $request, $id) {
        $request->validate([
            'bukti_foto' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
        ]);

        $loan = Loan::findOrFail($id);

        if ($loan->status !== 'menunggu_konfirmasi') {
            return back()->with('error', 'Status peminjaman tidak valid untuk proses pengembalian.');
        }

        $buktiPath = $request->file('bukti_foto')->store('bukti_pengembalian', 'public');
        $confirmationDate = now();
        $dueDate = $loan->tanggal_kembali_rencana->copy()->addDays(2);
        $daysLate = $confirmationDate->gt($dueDate) ? $confirmationDate->diffInDays($dueDate) : 0;
        $finePerDay = 5000;
        $totalFine = $daysLate * $finePerDay;

        $loan->update([
            'status' => 'kembali',
            'tanggal_kembali_aktual' => $confirmationDate,
            'petugas_id' => Auth::id(),
            'bukti_foto' => $buktiPath,
            'total_denda' => $totalFine
        ]);

        $tool = tools::findOrFail($loan->tool_id);
        $tool->increment('stok', $loan->jumlah ?? 1);

        ActivityLog::record('Process Return', 'Memproses pengembalian alat: ' . $tool->nama_alat . ' dengan denda: Rp ' . number_format($totalFine));

        return back()->with('success', 'Alat telah dikembalikan dengan bukti foto. Denda: Rp ' . number_format($totalFine));
    }

    public function confirmReturn($id) {
        $loan = Loan::findOrFail($id);

        if ($loan->status !== 'menunggu_konfirmasi') {
            return back()->with('error', 'Status peminjaman tidak valid untuk konfirmasi.');
        }

        $confirmationDate = now();
        $dueDate = $loan->tanggal_kembali_rencana->addDays(2);
        $daysLate = $confirmationDate->gt($dueDate) ? $confirmationDate->diffInDays($dueDate) : 0;
        $finePerDay = 5000; // Denda per hari terlambat
        $totalFine = $daysLate * $finePerDay;

        $loan->update([
            'status' => 'kembali',
            'tanggal_kembali_aktual' => $confirmationDate,
            'petugas_id' => Auth::id(),
            'total_denda' => $totalFine
        ]);

        // Kembalikan stok sesuai jumlah pinjam
        $tool = tools::findOrFail($loan->tool_id);
        $tool->increment('stok', $loan->jumlah ?? 1);

        ActivityLog::record('Confirm Return', 'Mengkonfirmasi pengembalian alat: ' . $tool->nama_alat . ' dengan denda: ' . number_format($totalFine));

        return back()->with('success', 'Pengembalian alat telah dikonfirmasi. Denda: Rp ' . number_format($totalFine));
    }

    public function report(Request $request) {
        // Bisa tambahkan filter tanggal jika mau
        $loans = Loan::with(['user', 'tool'])->get();
        return view('petugas.laporan', compact('loans'));
    }
}
