<?php

namespace App\Modules\Chat\Models;

use App\Modules\User\Models\Profile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Message extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'message',
        'attachments',
        'read_at'
    ];

    protected $hidden = [
        'profile',
        'recipient'
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
        ];
    }

    public function markAsRead(): void
    {
        if (is_null($this->read_at)) {
            DB::transaction(function () {
                $this->update(['read_at' => now()]);
            });
        }
    }

    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'recipient_id');
    }
}
