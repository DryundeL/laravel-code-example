<?php

namespace App\Modules\App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\App\Http\Requests\MaintenanceRequest;
use App\Modules\App\Http\Resources\MaintenanceResource;
use App\Modules\App\Services\MaintenanceService;
use App\Services\BaseService;
use Exception;

class MaintenanceController extends BaseController
{
    protected BaseService $service;

    public function __construct(MaintenanceService $service)
    {
        $this->service = $service;
    }

    /**
     * Display the specified resource.
     *
     * @return MaintenanceResource
     */
    public function getStatus(): MaintenanceResource
    {
        return $this->service->getStatus();
    }

    /**
     * Display the specified resource.
     *
     * @param MaintenanceRequest $request
     * @return MaintenanceResource
     * @throws Exception
     */
    public function changeStatus(MaintenanceRequest $request): MaintenanceResource
    {
        return $this->service->changeIsMaintenance($request->validated());
    }
}
