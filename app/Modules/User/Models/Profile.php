<?php

namespace App\Modules\User\Models;

use App\Models\User;
use App\Modules\Ai\Models\AiChatHistory;
use App\Modules\Announce\Models\AnnounceProfile;
use App\Modules\Chat\Models\Message;
use App\Modules\Notification\Models\Notification as NotificationModel;
use App\Modules\Notification\Models\OneSignalSubscription;
use App\Modules\Setting\Models\Settings;
use App\Modules\Story\Models\Story;
use App\Modules\Course\Models\Course;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;
use NotificationChannels\WebPush\HasPushSubscriptions;

class Profile extends Authenticatable
{
    use HasApiTokens, Notifiable, HasPushSubscriptions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_id',
        'group',
        'org',
        'calendar_token',
        'last_debt',
        'ban',
        'gid',
    ];

    protected function casts(): array
    {
        return [];
    }

    /**
     * Определяет канал вещания для уведомлений.
     *
     * @param Notification $notification
     * @return Channel|PrivateChannel|array
     */
    public function routeNotificationForBroadcast(Notification $notification): Channel|PrivateChannel|array
    {
        return new PrivateChannel('notifications.' . $this->id);
    }

    /**
     * Получить player IDs для OneSignal уведомлений
     *
     * @param Notification $notification
     * @return array|null
     */
    public function routeNotificationForOneSignal(Notification $notification): ?array
    {
        $allPlayerIds = $this->onesignalSubscriptions()->pluck('player_id')->toArray();

        if (empty($allPlayerIds)) {
            return null;
        }

        // Фильтруем только валидные UUID (OneSignal требует UUID формат)
        $validPlayerIds = array_filter($allPlayerIds, function ($playerId) {
            return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $playerId);
        });

        $validPlayerIds = array_values($validPlayerIds);

        if (empty($validPlayerIds)) {
            Log::channel('notifications')->warning(
                'OneSignal notification skipped: no valid UUID subscriptions',
                [
                    'profile_id' => $this->id,
                    'total_subscriptions' => count($allPlayerIds),
                ]
            );
            return null;
        }

        // Логируем только если были отфильтрованы невалидные ID
        if (count($allPlayerIds) !== count($validPlayerIds)) {
            Log::channel('notifications')->warning(
                'OneSignal: filtered invalid player_ids',
                [
                    'profile_id' => $this->id,
                    'total' => count($allPlayerIds),
                    'valid' => count($validPlayerIds),
                ]
            );
        }

        return $validPlayerIds;
    }

    public function unreadMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'recipient_id')->whereNull('read_at');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function access(): BelongsTo
    {
        return $this->belongsTo(Access::class);
    }

    public function stories(): BelongsToMany
    {
        return $this->belongsToMany(Story::class);
    }

    public function storyLikes(): BelongsToMany
    {
        return $this->belongsToMany(Story::class, 'story_likes')
            ->withTimestamps();
    }

    public function aiChatHistories(): HasMany
    {
        return $this->hasMany(AiChatHistory::class);
    }

    public function announces(): HasMany
    {
        return $this->hasMany(AnnounceProfile::class);
    }

    public function settings(): HasOne
    {
        return $this->hasOne(Settings::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class);
    }

    public function notifications(): BelongsToMany
    {
        return $this->belongsToMany(NotificationModel::class, 'notification_profile')
            ->withPivot(['text', 'is_read', 'id'])
            ->withTimestamps();
    }

    /**
     * Связь с OneSignal подписками для мобильных приложений
     */
    public function onesignalSubscriptions(): HasMany
    {
        return $this->hasMany(OneSignalSubscription::class);
    }

    /**
     * Обновить или создать OneSignal подписку
     *
     * @param string $playerId
     * @param string|null $deviceType
     * @return OneSignalSubscription
     */
    public function updateOneSignalSubscription(string $playerId, ?string $deviceType = null): OneSignalSubscription
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($playerId, $deviceType) {
            return OneSignalSubscription::updateOrCreate(
                [
                    'profile_id' => $this->id,
                    'player_id' => $playerId,
                ],
                [
                    'device_type' => $deviceType,
                ]
            );
        });
    }

    /**
     * Удалить OneSignal подписку по player_id
     *
     * @param string $playerId
     * @return bool
     */
    public function deleteOneSignalSubscription(string $playerId): bool
    {
        return $this->onesignalSubscriptions()
            ->where('player_id', $playerId)
            ->delete() > 0;
    }

    /**
     * @param array $filters
     * @return array
     */
    public function getNotificationsList(array $filters): array
    {
        $dbQuery = $this->notifications()->orderBy('notification_profile.created_at', 'desc');

        $limit = $filters['limit'] ?? 6;
        $offset = $filters['offset'] ?? 0;

        $total = $dbQuery->count();
        $notifications = $dbQuery->skip($offset)
            ->take($limit)
            ->get();

        $notificationIds = $notifications->pluck('id');
        $this->notifications()
            ->whereIn('notification_id', $notificationIds)
            ->updateExistingPivot($notificationIds, ['is_read' => true]);

        return [
            'notifications' => $notifications,
            'meta' => [
                'total' => $total,
                'offset' => $offset,
                'limit' => $limit,
            ]
        ];
    }

    /**
     * Скоуп для поиска незаблокированных профилей
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotBanned(Builder $query): Builder
    {
        return $query->where('ban', false);
    }
}
