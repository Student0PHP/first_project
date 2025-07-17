<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'provider_id', 'service_id',
        'total_time', 'earnings', 'status'
    ];
}
