<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $service_id
 * @property int|null $counter_id
 * @property string $queue_number
 * @property int $number
 * @property string $status
 * @property string|null $called_at
 * @property string|null $finished_at
 */
class Queue extends Model
{
    protected $fillable = [
        'service_id',
        'counter_id',
        'queue_number',
        'number',
        'status',
        'called_at',
        'finished_at'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function counter()
    {
        return $this->belongsTo(Counter::class);
    }
}
