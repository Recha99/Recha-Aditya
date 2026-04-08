<?php

namespace App\Models;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class tools extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function category()
        {
            return $this->belongsTo(Category::class, 'category_id');
        }
    public function loan()
        {
            return $this->hasMany(loan::class, 'tool_id');
        }
}
