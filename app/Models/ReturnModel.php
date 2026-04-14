<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnModel extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'user_id',
        'tool_id',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_kembali_aktual',
        'total_denda',
        'petugas_id',
        'bukti_foto'
    ];

    // Relasi ke user (peminjam)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke alat
    public function tool()
    {
        return $this->belongsTo(tools::class);
    }

    // Relasi ke petugas/admin
    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
