<?php

namespace App\Jobs;

use App\Modules\App\Services\MaintenanceService;
use App\Modules\Story\Models\Story;
use App\Modules\Story\Services\AdminStoryService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class InStudyAdminJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $event,
        public array  $payload
    )
    {
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $storyService = new AdminStoryService(new Story());
        $maintenanceService = new MaintenanceService();

        match ($this->event) {
            'stories.create' => $storyService->create($this->payload),
            'stories.update' => $storyService->update($this->payload, $this->payload['id']),
            'stories.pin' => $storyService->pinStory($this->payload['id']),
            'stories.unpin' => $storyService->unpinStory($this->payload['id']),
            'stories.delete' => $storyService->deleteStory($this->payload['id']),
            'maintenance.change' => $maintenanceService->changeIsMaintenance($this->payload),
            default => Log::warning('Неизвестное событие ' . $this->event),
        };
    }
}
