<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Loan;
use App\Models\tools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamController extends Controller
{
    public function index() {
        $tools = tools::with('category')->get();
        return view('peminjam.dashboard', compact('tools'));
    }

    public function store(Request $request) {
        $request->validate([
            'tanggal_kembali' => 'required|date|after_or_equal:today',
            'tools' => 'required|array'
        ]);

        $selectedTools = collect($request->input('tools', []))->filter(function ($item) {
            return !empty($item['selected']);
        });

        if ($selectedTools->isEmpty()) {
            return back()->withErrors(['tools' => 'Pilih minimal satu alat untuk dipinjam.'])->withInput();
        }

        $loanCount = 0;
        foreach ($selectedTools as $toolData) {
            $tool = tools::find($toolData['tool_id'] ?? null);
            if (!$tool) {
                return back()->withErrors(['tools' => 'Ada alat yang tidak ditemukan.'])->withInput();
            }

            $jumlah = intval($toolData['jumlah'] ?? 0);
            if ($jumlah < 1) {
                return back()->withErrors(['tools' => 'Jumlah pinjam harus minimal 1 untuk setiap alat yang dipilih.'])->withInput();
            }

            if ($jumlah > $tool->stok) {
                return back()->withErrors(['tools' => 'Jumlah pinjam untuk ' . $tool->nama_alat . ' tidak boleh lebih besar dari stok.'])->withInput();
            }

            Loan::create([
                'user_id' => Auth::id(),
                'tool_id' => $tool->id,
                'jumlah' => $jumlah,
                'tanggal_pinjam' => now() ->toDateString(),
                'tanggal_kembali_rencana' => $request->tanggal_kembali,
                'status' => 'pending'
            ]);

            $loanCount++;
        }

        ActivityLog::record('Ajukan Peminjaman', 'Mengajukan peminjaman ' . $loanCount . ' alat sekaligus.');

        return back()->with('success', 'Pengajuan berhasil, menunggu persetujuan.');
    }

    public function returnProsess(Request $request, $id) {
        $data = Loan::find($id);
        $tgl_kembali = now(); // Tanggal aktual pengembalian

        // Logika Denda
        $selisih = $data->tanggal_kembali_rencana->diffInDays($tgl_kembali, false); // Selisih hari, negatif jika terlambat
        $denda = 0;

        if ($selisih < 0) { // Jika terlambat
            $hari_terlambat = abs($selisih);
            if ($hari_terlambat > 2) {
                $denda = $hari_terlambat * 5000; // Denda per hari terlambat
            }
        }

        $data->update([
            'tanggal_kembali_aktual' => $tgl_kembali,
            'total_denda' => $denda,
            'status' => 'kembali'
        ]);
    }

    public function history() {
        $loans = Loan::where('user_id', Auth::id())
                    ->with('tool')
                    ->orderBy('created_at', 'desc')
                    ->get();
        return view('peminjam.riwayat', compact('loans'));
    }
}
