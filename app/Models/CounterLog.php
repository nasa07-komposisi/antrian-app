<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CounterLog extends Model
{
    protected $fillable = ['user_id', 'counter_id', 'login_at', 'logout_at'];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function counter()
    {
        return $this->belongsTo(Counter::class);
    }
}
