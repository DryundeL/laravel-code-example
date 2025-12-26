<?php

namespace App\Modules\Chat\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Broadcast::routes(['prefix' => 'api', 'middleware' => 'auth:profiles']);

        require base_path('app/Modules/Chat/Routes/channels.php');
    }
}
