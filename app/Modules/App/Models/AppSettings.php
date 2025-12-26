<?php

namespace App\Modules\App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSettings extends Model
{
    protected $fillable = [
        'is_maintenance',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'is_maintenance' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];
}
