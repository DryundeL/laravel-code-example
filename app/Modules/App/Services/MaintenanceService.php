<?php

namespace App\Modules\App\Services;

use App\Modules\App\Http\Resources\MaintenanceResource;
use App\Modules\App\Models\AppSettings;
use App\Services\BaseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class MaintenanceService extends BaseService
{

    /**
     * Get current status
     *
     * @return MaintenanceResource
     */
    public function getStatus(): MaintenanceResource
    {
        $appSettings = AppSettings::first();
        $now = Carbon::now();

        if ($appSettings->start_date && $now->greaterThanOrEqualTo(Carbon::parse($appSettings->start_date))) {
            $appSettings->is_maintenance = true;
        }

        if ($appSettings->end_date && $now->greaterThan(Carbon::parse($appSettings->end_date))) {
            $appSettings->is_maintenance = false;
        }

        if ($appSettings->isDirty()) {
            DB::transaction(function () use ($appSettings) {
                $appSettings->save();
            });
        }

        return new MaintenanceResource($appSettings);
    }

    /**
     * @throws Exception
     */
    public function changeIsMaintenance(array $attributes): array|object
    {
        $appSettings = DB::transaction(function () use ($attributes) {
            $appSettings = AppSettings::first();
            $appSettings->fill($attributes);
            $appSettings->save();

            return $appSettings;
        });

        return new MaintenanceResource($appSettings);
    }
}
