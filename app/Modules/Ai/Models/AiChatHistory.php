<?php

namespace App\Modules\Ai\Models;

use App\Modules\User\Models\Profile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiChatHistory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'query',
        'response',
    ];

    protected $hidden = [
        'profile',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    protected function casts(): array
    {
        return [
            'response' => 'array',
        ];
    }
}
