<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class tools extends Model
{
    protected $guarded = [];
    public function category()
        {
            return $this->belongsTo(category::class, 'category_id');
        }
    public function loan()
        {
            return $this->hasMany(loan::class);
        }
}
