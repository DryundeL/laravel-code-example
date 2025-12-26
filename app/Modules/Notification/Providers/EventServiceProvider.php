<?php

namespace App\Modules\Notification\Providers;

use App\Modules\Notification\Events\Notification;
use App\Modules\Notification\Listeners\SendWebPush;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as BaseServiceProvider;

class EventServiceProvider extends BaseServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Notification::class => [
            SendWebPush::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
