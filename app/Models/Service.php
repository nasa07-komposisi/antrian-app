<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $prefix
 * @property string|null $description
 * @property string|null $color_class
 * @property string|null $hex_color
 * @property int|null $daily_quota
 * @property string|null $quota_date
 */
class Service extends Model
{
    protected $fillable = ['name', 'prefix', 'description', 'color_class', 'hex_color', 'daily_quota', 'quota_date'];

    public function counters()
    {
        return $this->hasMany(Counter::class);
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }
}
