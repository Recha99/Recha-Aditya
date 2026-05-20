<?php

namespace App\Models;

use App\Models\Tool;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;


class loan extends Model
{

    protected $guarded = [];
        public function user()
            {
                return $this->belongsTo(User::class, 'user_id');
            }
        public function tool()
            {
                return $this->belongsTo(tools::class, 'tool_id');
            }
        public function petugas()
            {
                return $this->belongsTo(User::class, 'petugas_id');
            }

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali_rencana' => 'date',
        'tanggal_kembali_aktual' => 'date',
    ];
}
