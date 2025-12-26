<?php

namespace App\Modules\Notification\Models;

use App\Modules\User\Models\Profile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnesignalSubscription extends Model
{
    protected $fillable = [
        'profile_id',
        'player_id',
        'device_type',
    ];

    /**
     * Связь с профилем пользователя
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}

