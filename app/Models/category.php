<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class category extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function tools()
    {
        return $this->belongsTo(tools::class);
    }
}
