<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'action', 'description'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    //fungsi Helper
    public static function record($action, $desc = null) {
        if (!Auth::check()) return; // cegah error jika tidak ada user yang login (misal saat seeding)
        self::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $desc,
        ]);
    }

    public static function recordWithUser($userId, $action, $desc = null) {
        self::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $desc,
        ]);
    }

}

