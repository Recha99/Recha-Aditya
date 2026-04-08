<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


Class Category extends Model
{
    use HasFactory;
    protected $fillable = ['nama_kategori'];

    public function tools()
    {
        return $this->hasMany(tools::class); // Pastikan 'category_id' adalah nama kolom foreign key di tabel tools yang mengacu ke categories
    }
}
