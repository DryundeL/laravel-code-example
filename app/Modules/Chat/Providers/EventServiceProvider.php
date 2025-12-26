<?php

namespace App\Modules\Chat\Providers;

use App\Modules\Chat\Events\MessageSent;
use App\Modules\Chat\Listeners\UpdateUnreadCount;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as BaseServiceProvider;

class EventServiceProvider extends BaseServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        MessageSent::class => [
            UpdateUnreadCount::class,
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
