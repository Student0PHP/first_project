<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'provider_id',
        'service_id',
        'total_time',
        'earnings',
        'status'
    ];

    public function getTotalTimeInSecondsAttribute(): int
    {
        $timeString = $this->attributes['total_time'] ?? '';
        if (empty($timeString) || !preg_match('/^(\d{1,2}):(\d{2}):(\d{2})$/', $timeString, $matches)) {
            return 0;
        }
        $hours = (int)$matches[1];
        $minutes = (int)$matches[2];
        $seconds = (int)$matches[3];
        return $hours * 3600 + $minutes * 60 + $seconds;
    }
}
