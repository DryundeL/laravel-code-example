<?php

namespace App\Jobs;


use App\Models\User;
use App\Modules\Auth\Services\AuthService;
use App\Modules\User\Services\Handlers\DefaultUserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class IntegrationEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $event,
        public array  $payload
    )
    {
    }

    public function handle(): void
    {
        $userService = new DefaultUserService(new User());
        $authService = new AuthService();

        match ($this->event) {
            'create.user' => $userService->create($this->payload),
            'logout' => $authService->logoutUserByEmail($this->payload['email']),
            default => Log::warning('Неизвестное событие ' . $this->event),
        };
    }
}

