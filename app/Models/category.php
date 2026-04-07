<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

Class category extends Model
{
    use HasFactory;
    protected $fillable = ['nama_kategori'];

    public function tools(){
        {
            // Logika untuk menampilkan alat berdasarkan kategori
            return $this->hasMany(tools::class); // Pastikan 'category_id' adalah nama kolom foreign key di tabel tools yang mengacu ke categories
        }
    }
}
