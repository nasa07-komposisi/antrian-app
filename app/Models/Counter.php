<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int|null $occupied_by
 */
class Counter extends Model
{
    protected $fillable = ['service_id', 'user_id', 'occupied_by', 'name', 'status'];

    public function occupiedBy()
    {
        return $this->belongsTo(User::class, 'occupied_by');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }
}
